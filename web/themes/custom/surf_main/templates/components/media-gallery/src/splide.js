import Splide from "@splidejs/splide";
((Drupal, once) => {
  Drupal.behaviors.surf_institute_splide = {
    attach(context, _settings) {

      const classesToObserve = ['.c-media-gallery', '.splide__arrow--prev', '.splide__arrow--next'];

      once('splide', '.splide', context).forEach((splideElement) => {
        const splide = new Splide(splideElement, {
          lazyLoad: 'nearby',
          type: 'loop',
          autoHeight: true,
          pagination: false,
          autoplay: false,
          arrows: true,
          keyboard: true,
          perPage: 1,
          mediaQuery: 'min',
          arrowPath: 'm22.81 9.286-3.03 3.03 5.432 5.434H6.143v4.286h19.069l-5.432 5.432 3.03 3.03 10.607-10.605z',
          // fixedHeight: 210,
          breakpoints: {
            0: {
              gap: 0,

              // fixedHeight: 330,
            },
            768: {
              gap: 32,
              padding: '18%',
              // fixedHeight: 330,
            },
            1024: {
              gap: 32,
              padding: '18%',
              // fixedHeight: 454,
            },
            1620: {
              gap: 60,
              padding: '10%',
              // fixedHeight: 680,
            }
          }
        });
        splide.mount();

      });
      let resizeTimeout;
      window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
          classesToObserve.forEach(entry => {
            document.querySelector(entry).classList.add('visible')
          });
        }, 500);
      });

      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.intersectionRatio > 0.1) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });


      classesToObserve.forEach(className => {
        const elements = document.querySelectorAll(className);
        elements.forEach(element => {
          observer.observe(element);
        });
      });



    }
  }
})(Drupal, once);
