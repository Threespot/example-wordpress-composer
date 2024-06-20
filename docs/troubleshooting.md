# Troubleshooting

## Outline

- [Pulling Files and Database from Pantheon Test Environment](pulling-files-and-database-from-pantheon-test-environment)
- [ENOENT error when running lando start on macOS Big Sur](enoent-error-after-running-lando-start-on-macos-big-sur)

## Pulling Files and Database from Pantheon Test Environments

Running this command:

```
lando pull --database=test --files=test --code=none
```

causes this error:

```
Notice: Undefined index: X-Pantheon-Styx-Hostname in /var/www/html/vendor/pantheon-systems/terminus/src/Models/Environment.php on line 851
 [error]  Pantheon headers missing, which is not quite right.
```

The `test` enviroment has not been created. Dev needs promoted first before the files or database can be pulled from it.

## ENOENT error when running lando start on macOS Big Sur

When running this command on macOS Big Sur to start your application: 

```
lando start
```

You receive this error: 

```
Error =⇒ ENOENT: no such file or directory, scandir /Users/<your_username>/.ssh
```

This error appears where there isn't a `.ssh` folder inside of your root directory. To resolve the issue you can [generate a SSH key](https://pantheon.io/docs/ssh-keys#generate-ssh-key) which will create the missing `.ssh` folder in your root directory and a new ssh key. 