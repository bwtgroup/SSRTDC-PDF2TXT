**SSRTDC PDF2TXT**

This script is intended for conversion from PDF to TXT. It takes a directory with PDF files and returns a directory with TXT files.

 
####1. Dependencies
 * php 5.6 
 * smalot/pdfparser 
 
####2. Installation
 - Clone repository or download zip
 - Run in document root
 ```bash
 composer install
 ```
 
####3. Run Scripts for Project

run a command in command line, where PATH_TO_PDF is a path to the directory with PDFs. 
 ```bash
php index.php PATH_TO_PDF
```

####3.1. 

 If you want to show a work log, add  --log:
```bash
 php index.php PATH_TO_PDF --log
```

### 4. License
 This package is using [MIT](LICENSE.md).
