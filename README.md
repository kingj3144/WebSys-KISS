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

list:
user (foreign key)
item 
listId
category
orderNumber/timestamp

listAccess:
username (foreign key)
listId

Ownership:
username (foreign key)
name
listId