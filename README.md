# PikaZipCodeUpdates
zip code update script


Assumes the existence of a table structured like this:
```
CREATE TABLE `zip_codes` (
  `city` char(30) default NULL,
  `state` char(2) default NULL,
  `zip` char(5) default NULL,
  `area_code` char(3) default NULL,
  `county` char(27) default NULL
) ENGINE=MyISAM;
```

You need to enter your own zip-codes.com FTP credentials into the script. Also the name of your database. The script assumes it is called cms.
