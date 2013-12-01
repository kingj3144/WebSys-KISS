function getList(list, accessList, listid)
{
	document.getElementById("listContent").innerHTML = list;
	document.getElementById("accessContent").innerHTML = accessList;
	document.getElementById("listid").value = listid;
}