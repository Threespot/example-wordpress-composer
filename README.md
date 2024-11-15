# fixme-project-name

Note: Badges are auto-generated when creating a new WP site on Pantheon via Terminus. You can copy the Markdown from CircleCI in Project Settings -> Status Badges.

## Outline

- [Pantheon Environments](#pantheon-environments)
- [Local Development](#local-development)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Boot WordPress Application](#boot-word-press-application)
  - [Boot Theme (Webpack) Server](#boot-theme-webpack-server)
  - [Pull Files and Database from Pantheon](#pull-files-and-database-from-pantheon)
- [Deploying](#deploying)
  - [Deployment Using Terminus](#deployment-using-terminus)
    - [Test Environment](#test-environment)
    - [Live Environment](#live-environment)
- [Troubleshooting](/docs/troubleshooting.md)

## Pantheon Environments

- **Live** - https://live-fixme-app-name.pantheonsite.io
- **Test** - https://test-fixme-app-name.pantheonsite.io
- **Dev** - https://dev-fixme-app-name.pantheonsite.io

## Local Development

In order to more easily recreate the production environment locally, [Lando](https://lando.dev) is used for local development. We also use Pantheon’s CLI, Terminus, to sync files and databases.

- **PHP Server** - https://fixme-app-name.lndo.site/
- **Webpack Server** - https://localhost:3000

### Prerequisites

Install all the required local dependencies:

- [Git Version Control](https://git-scm.com/downloads)
- [Docker](https://www.docker.com/products/docker-desktop)
- Lando v3.0.6 ([Windows](https://docs.devwithlando.io/installation/windows.html), [macOS](https://docs.devwithlando.io/installation/macos.html))
- [Terminus](https://pantheon.io/docs/terminus/install/) 2.3.0, Pantheon’s CLI tool
- [Composer](https://getcomposer.org/doc/00-intro.md)
- [Node](https://nodejs.org/en/)  10.21.0
  Note: The sage theme dependencies do not support version of Node greater than 10. We recommend [asdf](https://github.com/asdf-vm/asdf) for managing multiple versions of Node
- Yarn
  ([Windows](https://yarnpkg.com/en/docs/install#windows-stable), [macOS](https://yarnpkg.com/en/docs/install#mac-stable))

You'll also need write access to this repo and be a member of the [Pantheon Project](https://dashboard.pantheon.io/sites/fixme-app-name#dev/code).

### Installation

1. Clone the repo
   `$ git clone https://github.com/Threespot/fixme-app-name.git`
1. Install application composer dependencies
   `$ composer install`
1. Install theme dependencies
   - Navigate to the theme directory
     `$ cd web/wp-content/themes/fixme-app-name`
   - Install composer dependencies
     `$ composer install`
   - Install Node dependencies
     `$ yarn install`

### Boot WordPress Application

After all dependencies are installed, navigate to the project root directory and run:

```
lando start
```

If this is the first time running this command, Lando will build the necessary Docker containers.

To stop the server run:

```
lando stop
```

Other Lando CLI command can be read here in the [Lando docs](https://docs.lando.dev/basics/usage.html)

### Boot Theme Server (Webpack)

Making CSS or JS updates requires running Webpack to recompile and inject the CSS and JS.

1. Navigate to the theme folder
   `$ cd /web/wp-content/themes/sage`

1. Install npm dependencies using Yarn
   `$ yarn install`

1. Start Webpack
   `$ yarn start`

1. You should now be able to view the site locally at https://localhost:3000
1. To stop the server, press <kbd>Control</kbd> + <kbd>C</kbd>

### Pull Files and Database from Pantheon

Lando is used to pull uploads and data from Pantheon. [See docs here](https://docs.lando.dev/config/pantheon.html#importing-your-database-and-files).

```
lando pull --database=test --files=test --code=none
```

## Deploying

Code committed to the remote `master` branch is automatically deployed to the `dev` environment on Pantheon. After a local branch is pushed, CircleCI will build and deploy the files to Pantheon’s dev environment. You can tell CircleCI to not run by adding `[skip ci]` to the commit message.

Code that exists on `dev` can be promoted to the `test` environment, and `test` can be promoted to the `live` environment. For more details about the application lifecycle, see https://pantheon.io/agencies/development-workflow/dev-test-live-workflow.
Feature branches with a corresponding pull request will create a multi-dev environment used for testing individual features. See https://pantheon.io/docs/multidev for documentation.

### Deployment Using Terminus

#### Test Environment

Code will be promoted from `dev` to `test`

```shell
lando composer run-script deploy:test
```

**NOTE:** this composer script will also purge Pantheon's cache.

or

```shell
lando terminus env:deploy fixme-app-name.test
```

#### Live Environment

Code will be promoted from `test` to `live`

```shell
lando composer run-script deploy:live
```

or

```shell
lando terminus env:deploy fixme-app-name.live
```