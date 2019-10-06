# Documentation

## Table of content
- [Documentation](#documentation)
  - [Table of content](#table-of-content)
  - [Useful links](#useful-links)
- [Metadocumentation](#metadocumentation)
  - [Shape](#shape)
  - [Content](#content)
    - [Api's documentation](#apis-documentation)
    - [Backend documentation](#backend-documentation)

## Useful links

- Associations portal frontend : https://github.com/simde-utc/portail-web
- Laravel 5.6 documentation : https://laravel.com/docs/5.6
- This rendered documentation : https://simde.gitlab.utc.fr/documentation

# Metadocumentation

*How to contribute to the documentation ?*

***NB :*** *Make sure you have read the guide in order to comment : [How to comment](portail/dev/backend/comment.md)*

## Shape

Organisation in two parts : one **documentation to use the api** and another **documentation about the api's code and architecture** (which we will call *backend*).

Each folder has its `_sidebar.md` where the *table of contents* is located and needs to be updated. You have to prefix every link by `portail/dev/`.

Each file has its *Table Of Contents* which must be up to date.


## Content

### Api's documentation

The goal of this documentation is to show the users the api and how to use it. Therefore, requests examples and answers (in json) will be included.

We are also going to show how to interact with the api in different languages. For instance, we will give examples of API connection code in js, php and python.

### Backend documentation

Its goal is to document the architecture, the created services, the controllers, the models and so on in order to give this project some durability. It doesn't aim to recreate Laravel's documentation. **We need to focus on what's specific to our project** 

Some examples :
- Oauth2 Implementation.
- Controller template.
- Visibility system.