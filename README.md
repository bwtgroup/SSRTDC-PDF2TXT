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
 
####3. Run Scripts for parse pdf to text

run a command in command line, where PATH_TO_PDF is a path to the directory with PDFs. 
 ```bash
php pdf2txt.php PATH_TO_PDF
```

####3.1. 

 If you want to show a work log, add  --log:
```bash
 php pdf2txt.php PATH_TO_PDF --log
```

####4. Run Scripts for create datasets from text files

run a command in command line
 ```bash
php txt2dataset.php --pfolder * --dfolder * --order * --incrsize *
```
where 
- pfolder - the name of the folder containing the TXT documents for forming the datasets. It assumes that the text files in this folder are named using the following convension:  <journal_ID>+”-”+<year>+<vol>+”(”+<issue>+”)-(”+<pages>+
            ”)-”+<DOI>+”.txt”. Hence, the information about the time of publication (timestamp) is encoded in the name of a file: <year>+<issue>.
- dfolder - the name of the folder to store the generated datasets
- order - the order in which the documents are picked to be added to datasets. Four different values are possible: (i) “chrono” for the chronological order; (ii) “rev-chrono” for counter-chronological order; (iii) “bi-dir” for bi-directional order; and (iv) “random” for picking the documents randomly
- incrsize - the number of papers to be included in a dataset increment
####3.1. For Madrid

run a command in command line
 ```bash
php txt2dataset_madrid.php --pfolder * --dfolder * --order * --incrsize *
```

### 4. License
 This package is using [MIT](LICENSE.md).
