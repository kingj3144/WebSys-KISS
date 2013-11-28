WebSys-KISS
===========

A Web Based Kitchen Inventory Supply System

database structure:

users:
name
username
email
password

salts:
user (foreign key)
salt

listitems:
user (foreign key)
item 
listId
category
orderNumber/timestamp

listaccess:
username (foreign key)
listId

ownership:
username (foreign key)
name
listId