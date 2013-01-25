<?PHP
session_start();
$page="event.php";
$script="../scripts/update_event.php";
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
include_once("../header.inc.php");
include_once("../include/functions.php");;
$idForCal=trim(dechiffre(hex2bin("$_GET[awq]"), "$_SESSION[UNIQID]"));
$typeForCal=trim(dechiffre(hex2bin("$_GET[zxs]"), "$_SESSION[UNIQID]"));
//echo "$_GET[awq] ** $idForCal ** $typeForCal ** $_SESSION[UNIQID]";
entete_page('','../');

?>
<script type="text/javascript" src="../content/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
<!-- Calendrier -->      
<link rel="stylesheet" type="text/css" href="../content/calendar/calendar-blue2.css">
<script type="text/javascript" src="../content/calendar/calendar.js"></script>
<script type="text/javascript" src="../content/calendar/lang/calendar-fr.js"></script>
<script type="text/javascript" src="../content/calendar/calendar-setup.js"></script>
<script src="../content/wdCalendar/wdCalendar/src/Plugins/Common.js" type="text/javascript"></script>        
<script src="../content/wdCalendar/wdCalendar/src/Plugins/datepicker_lang_FR.js" type="text/javascript"></script>        
<script src="../content/wdCalendar/wdCalendar/src/Plugins/jquery.datepicker.js" type="text/javascript"></script> 
<script src="../content/wdCalendar/wdCalendar/src/Plugins/jquery.dropdown.js" type="text/javascript"></script>  
<script src="../content/wdCalendar/wdCalendar/src/Plugins/jquery.colorselect.js" type="text/javascript"></script> 
<script src="../content/wdCalendar/wdCalendar/src/Plugins/jquery.form.js" type="text/javascript"></script>     
<script src="../content/wdCalendar/wdCalendar/src/Plugins/jquery.validate.js" type="text/javascript"></script>  
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("input").focus(function() {
		jQuery("#mess").hide();
	});

	jQuery.validator.addMethod("greaterThan", 
	function(value, element, params) {

	    if (!/Invalid|NaN/.test(new Date(value))) {
	        return new Date(value) >= new Date($(params).val());
	    }

	    return isNaN(value) && isNaN($(params).val()) 
	        || (parseFloat(value) > parseFloat($(params).val())); 
	},'Must be greater than {0}.');	

/*	$("#data2").rules('add', { greaterThan: "#data1" });
	or

	$("form").validate({
	    rules: {
	        EndDate: { greaterThan: "#data1" }
	    }
	});
*/
		
	jQuery("#myForm").validate({
		debug: false,
		rules: {
			event: "required",		
			date_debut: "required",
			heure_debut: "required",
			date_fin: {
				required: true,
				greaterThan: "#data1"
			},
			heure_fin: "required",
			description: "required",			
			mail1: {
				required: true,
				email: true
			}
		},
		messages: {
			event: "Ce champ est obligatoire",
			date_debut: "Ce champ est obligatoire",	
			heure_debut: "Ce champ est obligatoire",
			date_fin : {	
				required: "Ce champ est obligatoire",
				greaterThan:"La date de fin ne peut être inférieur à la date de début"
			},	
			heure_fin: "Ce champ est obligatoire",	
			description: "Ce champ est obligatoire",				
			mail1: "Une adresse mail valide est obligatoire",
		},
		submitHandler: function(form) {
			// do other stuff for a valid form
			/*jQuery.post('<?php echo $script;?>', jQuery("#myForm").serialize(), function(data) {
				jQuery('#mess').html(data);
			});*/	
			var datas = jQuery("#myForm").serialize();		
            jQuery.ajax({
                cache: false,
                type: 'POST',
                data: datas,
                url : '<?php echo $script;?>',
                success: function (response) {                    
                    jQuery("#mess").attr('class','mess');
                    jQuery("#mess").show(1500);
                    jQuery("#mess").html(response);
                },
                error: function(data, textStatus, jqXHR) {
                	jQuery("#mess").attr('class','messErr');
                    jQuery("#mess").show(1500);
                    jQuery("#mess").html("data");                	
                }
            })
		}
	});
});
</script>
<script type="text/javascript">
        /*if (!DateAdd || typeof (DateDiff) != "function") {
            var DateAdd = function(interval, number, idate) {
                number = parseInt(number);
                var date;
                if (typeof (idate) == "string") {
                    date = idate.split(/\D/);
                    eval("var date = new Date(" + date.join(",") + ")");
                }
                if (typeof (idate) == "object") {
                    date = new Date(idate.toString());
                }
                switch (interval) {
                    case "y": date.setFullYear(date.getFullYear() + number); break;
                    case "m": date.setMonth(date.getMonth() + number); break;
                    case "d": date.setDate(date.getDate() + number); break;
                    case "w": date.setDate(date.getDate() + 7 * number); break;
                    case "h": date.setHours(date.getHours() + number); break;
                    case "n": date.setMinutes(date.getMinutes() + number); break;
                    case "s": date.setSeconds(date.getSeconds() + number); break;
                    case "l": date.setMilliseconds(date.getMilliseconds() + number); break;
                }
                return date;
            }
        }*/
        function getHM(date)
        {
             var hour =date.getHours();
             var minute= date.getMinutes();
             var ret= (hour>9?hour:"0"+hour)+":"+(minute>9?minute:"0"+minute) ;
             return ret;
        }
        $(document).ready(function() {
            //debugger;
            var DATA_FEED_URL = "php/datafeed.php";
            var arrT = [];
            var tt = "{0}:{1}";
            for (var i = 7; i <= 19; i++) {                
                if (i==19){
                	arrT.push({ text: StrFormat(tt, [i >= 10 ? i : "0" + i, "00"]) }, '');
                }else{
                	arrT.push({ text: StrFormat(tt, [i >= 10 ? i : "0" + i, "00"]) }, { text: StrFormat(tt, [i >= 10 ? i : "0" + i, "30"]) });
                }                    
            }
            $("#timezone").val(new Date().getTimezoneOffset()/60 * -1);
            $("#stparttime").dropdown({
                dropheight: 200,
                dropwidth:60,
                selectedchange: function() { },
                items: arrT
            });
            $("#etparttime").dropdown({
                dropheight: 200,
                dropwidth:60,
                selectedchange: function() { },
                items: arrT
            });
            /*var check = $("#IsAllDayEvent").click(function(e) {
                if (this.checked) {
                    $("#stparttime").val("00:00").hide();
                    $("#etparttime").val("00:00").hide();
                }
                else {
                    var d = new Date();
                    var p = 60 - d.getMinutes();
                    if (p > 30) p = p - 30;
                    d = DateAdd("n", p, d);
                    $("#stparttime").val(getHM(d)).show();
                    $("#etparttime").val(getHM(DateAdd("h", 1, d))).show();
                }
            });
            if (check[0].checked) {
                $("#stparttime").val("00:00").hide();
                $("#etparttime").val("00:00").hide();
            }
            $("#Savebtn").click(function() { $("#fmEdit").submit(); });
            $("#Closebtn").click(function() { CloseModelWindow(); });
            $("#Deletebtn").click(function() {
                 if (confirm("Voulez-vous vraiment suppriemer cet événement ?")) {  
                    var param = [{ "name": "calendarId", value: 8}];                
                    $.post(DATA_FEED_URL + "?method=remove",
                        param,
                        function(data){
                              if (data.IsSuccess) {
                                    alert(data.Msg); 
                                    CloseModelWindow(null,true);                            
                                }
                                else {
                                    alert("Erreur :\r\n" + data.Msg);
                                }
                        }
                    ,"json");
                }
            });
            
           $("#stpartdate,#etpartdate").datepicker({ picker: "<button class='calpick'></button>"});    
            var cv =$("#colorvalue").val() ;
            if(cv=="")
            {
                cv="-1";
            }
            $("#calendarcolor").colorselect({ title: "Couleur", index: cv, hiddenid: "colorvalue" });
            //to define parameters of ajaxform
            var options = {
                beforeSubmit: function() {
                    return true;
                },
                dataType: "json",
                success: function(data) {
                    alert(data.Msg);
                    if (data.IsSuccess) {
                        CloseModelWindow(null,true);  
                    }
                }
            };
            $.validator.addMethod("date", function(value, element) {                             
                var arrs = value.split(i18n.datepicker.dateformat.separator);
                var year = arrs[i18n.datepicker.dateformat.year_index];
                var month = arrs[i18n.datepicker.dateformat.month_index];
                var day = arrs[i18n.datepicker.dateformat.day_index];
                var standvalue = [year,month,day].join("-");
                return this.optional(element) || /^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1,3-9]|1[0-2])[\/\-\.](?:29|30))(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1,3,5,7,8]|1[02])[\/\-\.]31)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])[\/\-\.]0?2[\/\-\.]29)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:16|[2468][048]|[3579][26])00[\/\-\.]0?2[\/\-\.]29)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1-9]|1[0-2])[\/\-\.](?:0?[1-9]|1\d|2[0-8]))(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?:\d{1,3})?)?$/.test(standvalue);
            }, "Format de date invalide");
            $.validator.addMethod("time", function(value, element) {
                return this.optional(element) || /^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/.test(value);
            }, "Format d'heure invalide");
            $.validator.addMethod("safe", function(value, element) {
                return this.optional(element) || /^[^$\<\>]+$/.test(value);
            }, "$<> non permis");
            $("#fmEdit").validate({
                submitHandler: function(form) { $("#fmEdit").ajaxSubmit(options); },
                errorElement: "div",
                errorClass: "cusErrorPanel",
                errorPlacement: function(error, element) {
                    showerror(error, element);
                }
            });
            function showerror(error, target) {
                var pos = target.position();
                var height = target.height();
                var newpos = { left: pos.left, top: pos.top + height + 2 }
                var form = $("#fmEdit");             
                error.appendTo(form).css(newpos);
            }*/
        });
    </script> 
</head>
<body>
<div id="container2" style="width:450px;">	
		<div id="mess" style="display: none;"></div>
		<!-- <div id="help" title="Aide"><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html<?php echo $page;?>',headingText: 'Aide',preserveContent: false } )"></a></div> -->
	<div class="content">
		<?php 
		connectSQL();		
		$req="SELECT a.*,date_format(a.date_naissance, '%d-%m-%Y') as date_age,b.date_fin_contrat FROM enfant a, contrat b WHERE b.id_asm=$_COOKIE[ID_UTILISATEUR] and a.id=b.id_enfant AND a.id!=$idForCal ORDER BY date_naissance;";			
		//$req="select * from enfant";
		$rec=@mysql_query($req);
		$nbEnfant=mysql_num_rows($rec);	
		?>
		<form method="post" id="myForm" action="#">
		<input type="hidden" name="awq" value="<?php echo $_GET[awq];?>">
		<table cellspacing=0 cellpadding=0>
		<tr><td>Evénement</td><td colspan=2>: 
			<select name="event">
				<option></option>
				<option>Repas</option>
				<option>Sortie </option>
			</select></td></tr>
		<tr><td>Début</td><td style="width: 140px;">: <input name="date_debut" id="data1" size=5 value="<?php echo date('Y-m-d');?>">
		<img src="../graphs/icons/cal.png" id="f_trigger_a1"
				style="text-align: left;cursor: pointer; vertical-align: middle; margin: 0;pading: 0;"
				title="Choisissez une date"
				onmouseover="this.style.background='blue';"
				onmouseout="this.style.background=''"/>
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "data1",
					ifFormat       :    "%Y-%m-%d",
					button         :    "f_trigger_a1",
					singleClick    :    true
				});
				</script>
		</td><td style="text-align: left;">
		<input MaxLength="5" class="required time" id="stparttime" name="heure_debut" style="width:40px;" type="text" value="12:30" />			
		</td></tr>
		<tr><td>Fin </td><td>: <input name="date_fin" size=5 id="data2" value="<?php echo date('Y-m-d'); ?>">
		<img src="../graphs/icons/cal.png" id="f_trigger_a2"
				style="text-align: left;cursor: pointer; vertical-align: middle; margin: 0;pading: 0;"
				title="Choisissez une date"
				onmouseover="this.style.background='blue';"
				onmouseout="this.style.background=''"/>
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "data2",
					ifFormat       :    "%Y-%m-%d",
					button         :    "f_trigger_a2",
					singleClick    :    true
				});
				</script>
		</td><td><input MaxLength="5" class="required time" id="etparttime" name="heure_fin" style="width:40px;" type="text" value="13:30" /></td></tr>
		<tr><td>Description </td><td colspan=2>&nbsp;&nbsp;<textarea name="description" cols=50 rows=8></textarea></td></tr>
		<?php if ($nbEnfant>0){
			echo '<tr><td colspan=3>Dupliquer cet événement ? <input type="checkbox" name="dupliq" onclick="jQuery(\'#dupliqEvent\').toggle();"></td></tr>
			<tr id="dupliqEvent" style="display: none;">
				<td colspan=3>
					<select name="dupliqId[]" multiple>';
					while ($res=mysql_fetch_array(($rec))){
						echo "<option value=\"$res[id]\">$res[prenom] $res[nom]</option>";
					}
					echo '</select>
				</td>
			</tr>';
		}
		?>
		</table>
		<p id="validButton"><button type="submit" id="submitButton" name="valid" value="Valider" style="cursor: pointer;">Valider</button></p>
		</form>
	</div>
</div>
</body>
</html>