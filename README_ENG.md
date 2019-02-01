# table-site
Simple site from google table

Documentation for the 'table-site' project

Content:
1. General Description
2. Project structure
2.1. index.php
2.2. Controller.php
2.3. ini.json
3. Types of data
3.1. integer;
3.2. float;
3.3. link;
3.4. dropdown;
3.5. string;
4. The structure of the data table
5. Publication of the table
6. Amendment
6.1. Change site address
6.2. Change the address of the data table
7. Developer

1. General Description
The site is designed to display, sort and filter data from Google tables.


2. Project structure
The project consists of two scripts:
2.1. index.php - responsible for the initial loading of the site, sorting, styles and signals for the controller
2.2. Controller.php - filters and transfers data
2.3. ini.json - setting data


3. Types of data
Valid data types for the script:
3.1. integer
The data type for integers. A column with this type of data will have two buttons for filtering, the values ??of which are taken from the maximum number of columns ("Min" - "0% -80%" and "Max" - "90% -100%") in the left panel of the site.

3.2. float
Data type for fractional numbers. A column with this data type will have buttons for filtering (5-10, 10-20, 20-30, 30-40, ..., 90-100) in the left panel of the site.

3.3. link
Data type for links. The column with this data type must be placed after the text, which should have a link.

3.4. dropdown
The data type for the drop-down list. A column with this data type will have a drop-down list in the left pane of the site. The data will be displayed when selecting one of the elements of the drop-down list. The table should only be used once.

3.5. string
Data type for strings and other data types.


4. The structure of the data table
The table should have 2 main parts:
1) Parameters and headers (1-3 rows)
* 1st row - row for data types. Each column must have a filled cage. She is responsible for how the script will perceive your data columns. Valid values ??are integer, float, link, dropdown, string.
* 2nd row - row for column names for filtering. Each column must have a filled cage. Values ??in the cells of this row should not have a space (" ")
* 3rd row - row for column headings. Each column must have a filled cage. This cells of this row are displayed as column headings on the site.
For correct work for all cells in 1,2,3 rows must be filled.
2) Data (4 -... rows)
Starting from the 4th row there should be data to display. Restrictions on the number of rows there.


5. Publication of the table
In order for the script to be able to obtain data from the table, it is necessary to publish the table on the Internet. To do this, follow these steps:
1) in the upper left part of the screen, click on the "File" button;
2) select "Publish to the Internet";
3) in the appeared window, in the "Links" block, in the first drop-down list, select the page you need to publish (if you have 1 sheet and you did not change the sheet name, then by default "Sheet1"), and in the second drop-down list, select " Comma Separated Values ??(.csv) ";
4) Click the "Publish" button. The appearing link is the link that needs to be inserted into the script;
5) in the "Published content and settings" box, check the marker for against "Automatically re-publish when changes are made"

6. Amendment
All changes are made to the ini.json file.
6.1. Change site address
If the site is not in the main directory. Then you need to change the path for the controller.
For example, the site has a link: http: //test.te/table. It is necessary in the file for the parameter "controller_url" to register as:
"controller_url": "/table/Controller.php",
If the link is: http: //test.te/. That "controller_url" parameter should have the following value:
"controller_url": "/Controller.php",

6.2. Change the address of the data table
The address inserts the value of the "table_url" parameter as follows:
"table_url": "https://docs.google.com/spreadsheets/d/e/FK38_j/pub?gid=0&single=true&output=csv"
If you do not have a link or the link does not work, then you will get the link in paragraph 5. Publishing a table in step 4.

Example ini.json file:
{
  "controller_url": "/Controller.php",
  "table_url": "https://docs.google.com/spreadsheets/d/e/FK38_j/pub?gid=0&single=true&output=csv"
}


7. Developer
Project developer: Nazar Kalyan
E-mail: nazar.10.17k@gmail.com
