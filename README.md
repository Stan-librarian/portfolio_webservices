# portfolio_webservices
Interrogation des webservices de Portfolio via PHP

cf https://sigbadmin.wordpress.com/2017/06/22/tester-les-webservices-en-php/

Attention, il faut activer curl (en décommentant la ligne extension=php_curl.dll dans php.ini), si ce n’est pas fait.

À la date du 30/09/2017, les webservices suivants sont 
- login_request (Authentification)
- get_patron_summary_request (Détail d'un usager)
- current_loans_request / fetchLoans_request (Prêts d'un usager)
