# convert excel to json

## features

- excel file => json file
    + input/<project>/*.xls => output/<project>/*.json
- json file => utf8 json file
    + input/<project>/*.json => output/<project>/*.json

### excel filename rule

- <file name>.xls => folder/name
    + `-` is the foldername seperator.
        * config-error.xls => config/error
    + chars after `.` would be ignored.
        * a-b.config-error.beta.xls => a/b (`config-erro`, `beta` ignored.)
- excel sheet name => <sheet name>.json

###### example

```
.
├── input              (input source project files)
│   └── project
        ├── abc.json
│       └── config-lang.xls
├── output             (output result files)
│   └── project
│       ├── abc.json (utf8)
│       └── config
│           └── lang
│               ├── zh-tw.json
│               └── zh-cn.json
└── src                (converter source)
```

## usage

- `composer update` install all dependencies.
- put excel files into `input/<project>` folder.
- make sure the permission of folder `output/<project>` is 777.
- execute
    + web: open index.html.
    + cli: `php do.php -h`

## Q&A

#### Allowed memory size of 134217728 bytes ...

- try do single project or even single file.

#### chmod error ...

- delete all subfolders of output/
- make sure the output folder permission is 0777.