<?php

namespace Drupal\imce;

use Drupal\Component\Utility\Environment;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Imce container class for helper methods.
 */
class Imce {

  /**
   * Checks if a user has an imce profile assigned for a file scheme.
   */
  public static function access(AccountProxyInterface $user = NULL, $scheme = NULL) {
    return (bool) static::userProfile($user, $scheme);
  }

  /**
   * Returns a response for an imce request.
   */
  public static function response(Request $request, AccountProxyInterface $user = NULL, $scheme = NULL) {
    return static::userFM($user, $scheme, $request)->pageResponse();
  }

  /**
   * Returns a file manager instance for a user.
   */
  // @codingStandardsIgnoreLine
  public static function userFM(AccountProxyInterface $user = NULL, $scheme = NULL, Request $request = NULL) {
    if ($conf = static::userConf($user, $scheme)) {
      return new ImceFM($conf, $user, $request);
    }
  }

  /**
   * Returns imce configuration profile for a user.
   */
  public static function userProfile(AccountProxyInterface $user = NULL, $scheme = NULL) {
    $profiles = &drupal_static(__METHOD__, []);
    $user = $user ?: \Drupal::currentUser();
    $scheme = isset($scheme) ? $scheme : \Drupal::config('system.file')->get('default_scheme');
    $profile = &$profiles[$user->id()][$scheme];

    if (isset($profile)) {
      return $profile;
    }
    $profile = FALSE;

    if (\Drupal::service('stream_wrapper_manager')->getViaScheme($scheme)) {
      $storage = \Drupal::entityTypeManager()->getStorage('imce_profile');
      if ($user->id() == 1 && $profile = $storage->load('admin')) {
        return $profile;
      }
      $imce_settings = \Drupal::config('imce.settings');
      $roles_profiles = $imce_settings->get('roles_profiles');
      $user_roles = array_flip($user->getRoles());
      // Order roles from more permissive to less permissive.
      $roles = array_reverse(user_roles());
      foreach ($roles as $rid => $role) {
        if (isset($user_roles[$rid]) && !empty($roles_profiles[$rid][$scheme])) {
          if ($profile = $storage->load($roles_profiles[$rid][$scheme])) {
            return $profile;
          }
        }
      }
    }

    return $profile;
  }

  /**
   * Returns processed profile configuration for a user.
   */
  public static function userConf(AccountProxyInterface $user = NULL, $scheme = NULL) {
    $user = $user ?: \Drupal::currentUser();
    $scheme = isset($scheme) ? $scheme : \Drupal::config('system.file')->get('default_scheme');
    if ($profile = static::userProfile($user, $scheme)) {
      $conf = $profile->getConf();
      $conf['pid'] = $profile->id();
      $conf['scheme'] = $scheme;
      return static::processUserConf($conf, $user);
    }
  }

  /**
   * Processes raw profile configuration of a user.
   */
  public static function processUserConf(array $conf, AccountProxyInterface $user) {
    // Convert MB to bytes.
    $conf['maxsize'] *= 1048576;
    $conf['quota'] *= 1048576;
    // Check php max upload size.
    $phpmaxsize = Environment::getUploadMaxSize();
    if ($phpmaxsize && (!$conf['maxsize'] || $phpmaxsize < $conf['maxsize'])) {
      $conf['maxsize'] = $phpmaxsize;
    }
    // Set root uri and url.
    $conf['root_uri'] = $conf['scheme'] . '://';
    // file_create_url requires a filepath for some schemes like private:// .
    $url_gen = \Drupal::service('file_url_generator');
    $conf['root_url'] = preg_replace('@/(?:%2E|\.)$@i', '', $url_gen->generateAbsoluteString($conf['root_uri'] . '.'));
    // Convert to relative.
    if (!\Drupal::config('imce.settings')->get('abs_urls')) {
      $conf['root_url'] = $url_gen->transformRelative($conf['root_url']);
    }
    $conf['token'] = $user->isAnonymous() ? 'anon' : \Drupal::csrfToken()->get('imce');
    // Process folders.
    $conf['folders'] = static::processUserFolders($conf['folders'], $user);
    // Call plugin processors.
    \Drupal::service('plugin.manager.imce.plugin')->processUserConf($conf, $user);
    return $conf;
  }

  /**
   * Processes user folders.
   */
  public static function processUserFolders(array $folders, AccountProxyInterface $user) {
    $ret = [];
    $token_service = \Drupal::token();
    $meta = new BubbleableMetadata();
    $token_data = ['user' => User::load($user->id())];
    foreach ($folders as $folder) {
      $path = $folder['path'];
      if (strpos($path, '[') !== FALSE) {
        $path = $token_service->replace($path, $token_data, [], $meta);
        // Unable to resolve a token.
        if (strpos($path, ':') !== FALSE) {
          continue;
        }
      }
      if (static::regularPath($path)) {
        $ret[$path] = $folder;
        unset($ret[$path]['path']);
      }
    }
    return $ret;
  }

  /**
   * Checks a permission in a profile conf.
   */
  public static function permissionInConf($permission, array $conf) {
    if (!empty($conf['folders'])) {
      foreach ($conf['folders'] as $folder_conf) {
        if (static::permissionInFolderConf($permission, $folder_conf)) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   * Checks a permission in a folder conf.
   */
  public static function permissionInFolderConf($permission, $folder_conf) {
    if ($folder_conf && !empty($folder_conf['permissions'])) {
      $permissions = $folder_conf['permissions'];
      return isset($permissions[$permission]) ? (bool) $permissions[$permission] : !empty($permissions['all']);
    }
    return FALSE;
  }

  /**
   * Returns predefined/inherited configuration.
   *
   * Returns predefined/inherited configuration
   *   of a folder path in a profile conf.
   */
  public static function folderInConf($path, array $conf) {
    // Predefined.
    if (isset($conf['folders'][$path])) {
      return $conf['folders'][$path];
    }
    // Inherited.
    if (!empty($conf['folders']) && static::regularPath($path) && is_dir(static::joinPaths($conf['root_uri'], $path))) {
      foreach ($conf['folders'] as $folder_path => $folder_conf) {
        $is_root = $folder_path === '.';
        if ($is_root || strpos($path . '/', $folder_path . '/') === 0) {
          if (static::permissionInFolderConf('browse_subfolders', $folder_conf)) {
            // Validate the rest of the path.
            if ($filter = static::nameFilterInConf($conf)) {
              $rest = $is_root ? $path : substr($path, strlen($folder_path) + 1);
              foreach (explode('/', $rest) as $name) {
                if (preg_match($filter, $name)) {
                  return;
                }
              }
            }
            return $folder_conf + ['inherited' => TRUE];
          }
        }
      }
    }
  }

  /**
   * Returns name filtering regexp from a profile conf.
   */
  public static function nameFilterInConf(array $conf) {
    $filters = isset($conf['name_filters']) ? $conf['name_filters'] : [];
    if (empty($conf['allow_dot_files'])) {
      $filters[] = '^\.|\.$';
    }
    return $filters ? '/' . implode('|', $filters) . '/' : '';
  }

  /**
   * Splits a path into dirpath and filename.
   */
  public static function splitPath($path) {
    if (is_string($path) && $path != '') {
      $parts = explode('/', $path);
      $filename = array_pop($parts);
      $dirpath = implode('/', $parts);
      if ($filename !== '') {
        return [$dirpath === '' ? '.' : $dirpath, $filename];
      }
    }
  }

  /**
   * Creates a fle path by joining a dirpath and a filename.
   */
  public static function joinPaths($dirpath, $filename) {
    if ($dirpath === '.') {
      return $filename;
    }
    if ($filename === '.') {
      return $dirpath;
    }
    if (substr($dirpath, -1) !== '/') {
      $dirpath .= '/';
    }
    return $dirpath . $filename;
  }

  /**
   * Checks the structure of a folder path.
   *
   * Forbids current/parent directory notations.
   */
  public static function regularPath($path) {
    return is_string($path) && ($path === '.' || !preg_match('@\\\\|(^|/)\.*(/|$)@', $path));
  }

  /**
   * Returns the contents of a directory.
   */
  public static function scanDir($diruri, array $options = []) {
    $content = ['files' => [], 'subfolders' => []];
    $browse_files = isset($options['browse_files']) ? $options['browse_files'] : TRUE;
    $browse_subfolders = isset($options['browse_subfolders']) ? $options['browse_subfolders'] : TRUE;
    if (!$browse_files && !$browse_subfolders) {
      return $content;
    }
    if (!$opendir = opendir($diruri)) {
      return $content + ['error' => TRUE];
    }
    // Prepare filters.
    $name_filter = empty($options['name_filter']) ? FALSE : $options['name_filter'];
    $callback = empty($options['filter_callback']) ? FALSE : $options['filter_callback'];
    $uriprefix = substr($diruri, -1) === '/' ? $diruri : $diruri . '/';
    while (($filename = readdir($opendir)) !== FALSE) {
      // Exclude special names.
      if ($filename === '.' || $filename === '..') {
        continue;
      }
      // Check filter regexp.
      if ($name_filter && preg_match($name_filter, $filename)) {
        continue;
      }
      // Check browse permissions.
      $fileuri = $uriprefix . $filename;
      $is_dir = is_dir($fileuri);
      if ($is_dir ? !$browse_subfolders : !$browse_files) {
        continue;
      }
      // Execute callback.
      if ($callback) {
        $result = $callback($filename, $is_dir, $fileuri, $options);
        if ($result === 'continue') {
          continue;
        }
        elseif ($result === 'break') {
          break;
        }
      }
      $content[$is_dir ? 'subfolders' : 'files'][$filename] = $fileuri;
    }
    closedir($opendir);
    return $content;
  }

  /**
   * Returns a managed file entity by uri.
   *
   * Optionally creates it.
   *
   * @return \Drupal\file\FileInterface
   *   Drupal File entity.
   */
  public static function getFileEntity($uri, $create = FALSE, $save = FALSE) {
    $file = FALSE;
    if ($files = \Drupal::entityTypeManager()->getStorage('file')->loadByProperties(['uri' => $uri])) {
      $file = reset($files);
    }
    elseif ($create) {
      $file = static::createFileEntity($uri, $save);
    }
    return $file;
  }

  /**
   * Creates a file entity with an uri.
   *
   * @return \Drupal\file\FileInterface
   *   Drupal File entity.
   */
  public static function createFileEntity($uri, $save = FALSE) {
    $values = [
      'uri' => $uri,
      'uid' => \Drupal::currentUser()->id(),
      'status' => 1,
      'filesize' => filesize($uri),
      'filename' => \Drupal::service('file_system')->basename($uri),
      'filemime' => \Drupal::service('file.mime_type.guesser')->guessMimeType($uri),
    ];
    $file = \Drupal::entityTypeManager()->getStorage('file')->create($values);
    if ($save) {
      $file->save();
    }
    return $file;
  }

  /**
   * Checks if the selected file paths are accessible by a user with Imce.
   *
   * Returns the accessible paths.
   */
  public static function accessFilePaths(array $paths, AccountProxyInterface $user = NULL, $scheme = NULL) {
    $ret = [];
    if ($fm = static::userFM($user, $scheme)) {
      foreach ($paths as $path) {
        if ($fm->checkFile($path)) {
          $ret[] = $path;
        }
      }
    }
    return $ret;
  }

  /**
   * Checks if a file uri is accessible by a user with Imce.
   */
  public static function accessFileUri($uri, AccountProxyInterface $user = NULL) {
    list($scheme, $path) = explode('://', $uri, 2);
    return $scheme && $path && Imce::accessFilePaths([$path], $user, $scheme);
  }

}
