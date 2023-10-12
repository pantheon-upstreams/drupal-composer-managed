/// <reference types="cypress" />

context('Subtheme', () => {
  beforeEach(() => {
  })

  it('generates a subtheme', () => {
    cy.exec('rm -rf ../test/cypresstest');
    cy.exec('npm run subtheme', {
      env: {
        NAME: 'cypresstest',
        DIR: '../test'
      },
      timeout: 120000
    });
  })
})
