Fail2ban is a must have security tool on linux servers. 
But unfortunately it causes many misunderstandings with end users..
For example they get banned if they change a password for a mailbox in plesk, 
because their mailing tools try to authenticate more than once.
They may not know that they get banned and are not able to setup the mailbox.
So they will contact their admin.

fail2ban-alert shows you and your plesk users an alert if the own ip address is banned by fail2ban,
with additional information and the option to unban it.
Maybe this will help to reduce queries.