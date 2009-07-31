<?PHP
//include('template_pagestart.php');
include ('dblogin.php'); //connection in $db

?>




<script type="text/javascript">
function fillInPosedQuestion() {
	updateAll();
	//alert('Value of generalquestion is ' + document.getElementById("generalquestion").value);
	if (document.getElementById("generalquestion").value==0) {
		var elSel = document.getElementById('posedquestion');
		while (document.getElementById('posedquestion').length>0) {
			elSel.remove(0);
		}			
		var elOptNew = document.createElement('option');
		elOptNew.text = 'ANY (Select category first)';
		elOptNew.value = 0;
		
		try {
			elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
		}
		catch(ex) {
			elSel.add(elOptNew); // IE only
		}
	}
	else {
		updateoptionbox('posedquestion','posedquestion', document.getElementById("generalquestion").value);
	}
}; 
</script>


<br>Category: <select name="generalquestion" id="generalquestion" onChange="fillInPosedQuestion()"">
<?PHP
$_GET['table'] = 'generalquestion';
include ('optionbox_generic.php');
?>
</select> 
<?PHP
include 'templates/helppanel_generic.php';
?>


<br>Question: <select name="posedquestion" id="posedquestion" onchange="updateAll()">
<option value=0>ANY (Select category first)</option>
</select>


<?PHP
//include('template_pageend.php');
?>
