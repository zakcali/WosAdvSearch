# WosAdvSearch
Web of Science AdvancedSearch Query Creator for Universities

Web of Science is an indexing service for quality scientific publications worldwide. If you have an ip access, usually in an university campus area, you can search for publications and citations online at their web site: http://apps.webofknowledge.com/
"Basic Search" option can be redirected from html forms, described and accessed here: http://wokinfo.com/webtools/searchbox/

Unfortunately, there isn't any form (at least I couldn't find) for "Advanced Search" option. You must go to, http://apps.webofknowledge.com/ click "Advanced Search" option, enter search terms, and click Search button.
If you don't search regulary, and know what to do this procedure is okay. But if you want to search for a lot of authors, or search for a lot of departments in an university, this is waste of time.  
Those links decribe, what how to use advanced search: http://images.webofknowledge.com/WOKRS534DR1/help/WOS/hp_advanced_search.html
and http://images.webofknowledge.com/WOKRS534DR1/help/WOS/hp_advanced_examples.html

Web of Science has an SOAP api,named WokSearchLite the traget file is http://search.webofknowledge.com/esti/wokmws/ws/WokSearchLite and, you can access it within campus.
Here are online and pdf versions of documentation: http://help.incites.clarivate.com/wosWebServicesLite/WebServicesLiteOverviewGroup/Introduction.html and 
https://www.recursoscientificos.fecyt.es/sites/default/files/web_of_science_web_services_3_0.pdf
and here are presentaitons from Clarivate Analytics about WOS APIs: https://www.e-nformation.ro/wp-content/uploads/2018/11/APIs.pdf and https://wok.mimas.ac.uk/support/documentation/presentations/api1018.pdf

The hard way to use this api is, to use some api tools, as described here: http://www.dlib.org/dlib/march16/li/App2-WebServicesSetup.pdf
Best easy way is create a tool like http://wokinfo.com/webtools/searchbox/ and supply this tool with prepared query lists.



