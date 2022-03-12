# kw_menu

Mapping menu from different sources and make the tree necessary to process everything.
This version supports only files as entries in data source. If Storage is in database
it's possible to map that source. You can also make your own implementation
of IEntriesSource and IMetaSource to satisfy selection of available entries.

## PHP Installation

```
{
    "require": {
        "alex-kalanis/kw_menu": "1.0"
    }
}
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)


## PHP Usage

1.) Use your autoloader (if not already done via Composer autoloader)

2.) Add some external packages with connection to the local or remote services.

3.) Connect the "kalanis\kw_menu\MetaProcessor" into your app. Extends it for setting your case.

4.) Extend your libraries by interfaces inside the package.

5.) Just call setting and render
