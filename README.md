# WosAdvSearch
Web of Science AdvancedSearch Query Creator for Universities
# Introduction
Web of Science is an indexing service for quality scientific publications worldwide. If you have an ip access, usually in an university campus area, you can search for publications and citations online at their web site: http://apps.webofknowledge.com/
"Basic Search" option can be redirected from html forms, described and accessed here: http://wokinfo.com/webtools/searchbox/

Unfortunately, there isn't any form (at least I couldn't find) for "Advanced Search" option. You must go to, http://apps.webofknowledge.com/ click "Advanced Search" option, enter search terms, and click Search button.
If you don't search regulary, and know what to do this procedure is okay. But if you want to search for a lot of authors, or search for a lot of departments in an university, this is waste of time.  
Those links decribe, what how to use advanced search: http://images.webofknowledge.com/WOKRS534DR1/help/WOS/hp_advanced_search.html
and http://images.webofknowledge.com/WOKRS534DR1/help/WOS/hp_advanced_examples.html

Web of Science has an SOAP api,named WokSearchLite the traget file is http://search.webofknowledge.com/esti/wokmws/ws/WokSearchLite and, you can access it within campus.
Here are online and pdf versions of documentation: http://help.incites.clarivate.com/wosWebServicesLite/WebServicesLiteOverviewGroup/Introduction.html and 
https://www.recursoscientificos.fecyt.es/sites/default/files/web_of_science_web_services_3_0.pdf
and here are presentaitons from Clarivate Analytics about WOS APIs: https://www.e-nformation.ro/wp-content/uploads/2018/11/APIs.pdf and https://wok.mimas.ac.uk/support/documentation/presentations/api1018.pdf and https://bg.pw.edu.pl/images/OIN/Omega_PSIR/Web_of_Science_and_Incites_data_Integration_Clarivate_Analytics.pdf

The hard way to use this api is, to use some api tools, as described here: http://www.dlib.org/dlib/march16/li/App2-WebServicesSetup.pdf
and here: https://sc.lib.miamioh.edu/bitstream/handle/2374.MIA/6053/SOAP%20-%20Publisher%27s%20Version-LHTN-08-2015-0059.pdf?sequence=1&isAllowed=y

Best way is create a tool like http://wokinfo.com/webtools/searchbox/ and supply this tool with prepared query lists.

There are Python source codes for querying wos programmatically, such as https://github.com/enricobacis/wos , https://github.com/karthik/RRR and https://github.com/MSU-Libraries/wos

Since my university supply me a server with php capabilities, I had to search for php source codes, like https://gist.github.com/pol/1321660 and https://gist.github.com/domoritz/2012629

# Logic
There are 3 vertical tabs.

1- First vertical tab finds publications from various indexes, and creates queries for Web of Science Advanced Search Tab.

departments and associated queries are read from the file named: department-list.csv, by using php script as follows
json_encode(file_get_contents('department-list.csv')); 

and parsed with papaparse.min.js library, source: https://www.papaparse.com

then a menu is created with html select tag 

When user selects a department from list, associated query text is copied to a textarea, named wosQuery, id is searchText

Now user has two options:
a) By clicking the button, below the textarea, http://apps.webofknowledge.com/WOS_AdvancedSearch_input.do?&product=WOS&search_mode=AdvancedSearch page is opene in a new tab, and clipboard contains the query text. User must paste querytext to search area, and click Search button in Web of Science site

b) By clicking the button, above the textarea, wos-retrieve.php function is called, and queries on the server side by using WokSearchLite, and creates an output containing publications, and links to publications and citations


