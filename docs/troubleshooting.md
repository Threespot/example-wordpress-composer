# Troubleshooting

## Outline

- [Pulling Files and Database from Pantheon Test Environment](pulling-files-and-database-from-pantheon-test-environment)
- [Lando won't build due to a missing ssh file](lando-wont-build-due-to-a-missing-ssh-file)

## Pulling Files and Database from Pantheon Test Environment

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

## Lando won't build due to a missing ssh file

What the error looks like:

```
Error =⇒ ENOENT: no such file or directory, scandir /Users/<your_username>/.ssh
```

Solution: 

This error occurs when you haven't generated an SSH key on your machine. Following Pantheon's documentation on [how to generate a SSH key](https://pantheon.io/docs/ssh-keys#generate-ssh-key) will resolve this issue. 