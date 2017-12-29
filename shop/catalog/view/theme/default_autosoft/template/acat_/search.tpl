<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>


<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?echo $heading_title; ?></h1>
	
<div id="acatwrkspace" style="background:#eee; border:1px solid #666; text-align:left;">
	<div id="acatinnerspace" style="padding:10px 10px 10px 10px;">
		
		
		<div id="searchdiv" class="searchdiv">
<h1><center>Поиск</center></h1>
<form name="acatsearch" id="acatsearch">
<input type="hidden" name="ta" id="ta" value="">
<input type="hidden" name="marks" id="marks" value="">
<input type="hidden" name="models" id="models" value="">
<!--<input type="hidden" name="page" value="1"> -->
<input type="hidden" name="group" value="1">
<input type="hidden" name="order" value="0">
<table width="100%" border="0">
<tr>
	<td width="30%">Заполните строку поиска (Заводской номер, часть названия запасной части):</td>
	<td><input type="text" name="search" id="search_ext" style="width:100%"><br>
		<input type="checkbox" id="fullmatch" name="fullmatch">точное совпадение</input>
	</td>
</tr>
<tr>
	<td colspan="2" align="center"><span class="acatadvsearchlink" onClick="$('#AcatAdvSearch').slideToggle(200);">Расширенный поиск</span></td>
</tr>
    

<tr>
	<td colspan="2">
    <div  id="AcatAdvSearch">
    	<table width="100%" class="acatadvsearchtable">
            <tr>
            	<td>Группы автотехники</td>
            	<td width="80%"><select id="acatsearchta" style="width:100%;" onchange="getSearchDrop('mark',types_arr);">
                <option value="-1">Все</option>
                </select>
                </td>
            </tr>
            <tr>
            	<td>Марки автотехники</td>
            	<td><select id="acatsearchmark" style="width:100%" onchange="getSearchDrop('model',types_arr,marks_arr);">
                <option value="-1">Все</option>
                </select>
                </td>
            </tr>
            <tr>
            	<td>Модели автотехники</td>
            	<td><select id="acatsearchmodel" style="width:100%" disabled="disabled" onchange='if ($("#acatsearchmodel").val()==-1) { $("#models").val("");  } else {$("#models").val($("#acatsearchmodel").val());}'>
                <option value="-1">Все</option>
                </select>
                </td>
            </tr>
        </table>
        </div>
    </td>
</tr>
            

<tr><td colspan="2" align="right"><input type="button" value="Искать" onClick="getSearch('')"></td></tr>
</table>
</form>
</div>
<script type="text/javascript">getSearchDrop('ta');</script>
<script type="text/javascript">
	$('#search_ext').keydown(function(e) {
		if (e.keyCode == 13) { getSearch(''); return false;}
	});
</script>
		
		
		
	</div>
</div>

<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>

<script type="text/javascript">
			AcatInit('<?php echo HTTP_SERVER.'autosoft/acat/scheme/'.AC_CHEMA.'/'?>');
			<?php global $ACAT_PARAMS; if (isset($searchstr)) { echo "$('#search_ext').val('".$searchstr."'); getSearch('');"; } ?>
</script>