/** fogtransfert.css
**	Fonctions masquage JS pour FOG Transfert
**/

function contenu_grey()
{
	div = document.getElementById('contenu_grey');
	if(div.style.display == 'none')
	{
		div.style.display = 'block';
	}
	else
	{
		div.style.display = 'none';
	}
}

function contenu_green()
{
	div = document.getElementById('contenu_green');
	if(div.style.display == 'none')
	{
		div.style.display = 'block';
	}
	else
	{
		div.style.display = 'none';
	}
}

function contenu_orange()
{
	div = document.getElementById('contenu_orange');
	if(div.style.display == 'none')
	{
		div.style.display = 'block';
	}
	else
	{
		div.style.display = 'none';
	}
}

function contenu_red()
{
	div = document.getElementById('contenu_red');
	if(div.style.display == 'none')
	{
		div.style.display = 'block';
	}
	else
	{
		div.style.display = 'none';
	}
}

function check_all_checkboxes(source)
{
	checkboxes = document.getElementsByName('checkbox[]');
	for(var i=0, n=checkboxes.length;i<n;i++)
	{
		checkboxes[i].checked = source.checked;
	}
}
