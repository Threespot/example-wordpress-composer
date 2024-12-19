# Troubleshooting

## Outline

- [Pulling Files and Database from Pantheon Test Environment](pulling-files-and-database-from-pantheon-test-environment)
- [Cache Must Be Present and Writeable](cache-must-be-present-and-writeable)

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



## Cache Must Be Present and Writeable

```
Warning: mkdir): No such file or directory in /code/vendor/illuminate/filesystem/Filesystem.php on line 636 Fatal error: Uncaught Exception: The /code/web/wp-content/cache/acorn/framework/cache directory must be present and writable. in
```

Pantheon security update requires deleting a file from the server. Step to complete include

1. Place environment in to `SFTP` mode.
2. Connect to the server using FTP client or via SSH
3. Delete the following file `/code/vendor/monolog/monolog/src/Monolog/Handler/TelegramBotHandler.php`
4. Restore site to `git` mode. 
