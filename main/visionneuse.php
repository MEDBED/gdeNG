<?PHP
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();
$titre="Visionneuse";
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");
include_once("../include/textes.php");
entete_page($GLOBALS['params']['appli']['title_appli']." - ".$titre,'');
?>
<!-- Visionneuse -->      
<link rel="stylesheet" type="text/css" href="../content/gallery/basic.css">
<link rel="stylesheet" type="text/css" href="../content/gallery/galleriffic-5.css">
<link rel="stylesheet" type="text/css" href="../content/gallery/black.css">

<script type="text/javascript" src="../content/jquery-1.8.1.min.js"></script>
<script type="text/javascript" src="../content/gallery/jquery.history.js"></script>
<script type="text/javascript" src="../content/gallery/jquery.galleriffic.js"></script>
<script type="text/javascript" src="../content/gallery/jquery.opacityrollover.js"></script>
<script type="text/javascript" src="../content/gallery/jquery.flyout-1.2.min.js"></script>
<script type="text/javascript">
    document.write('<style>.noscript { display: none; }</style>');
</script>
</head>
<body>   
    <div id="page">
    <div id="container">
    <div class="navigation-container">    
        <div id="thumbs" class="navigation">
            <a class="pageLink prev" style="visibility: hidden;" href="#" title="Previous Page"></a>
            <ul class="thumbs noscript">                
                <?php
                connectSQL();	                
                if ($_GET['affAll']==1){
                    $requete="SELECT a.*,a.id as id_document,a.createOn as createOnDoc,a.updateOn as updateOnDoc,CONCAT(b.nom,' ',prenom) AS utilisateur FROM document a, user b, materiel c WHERE (a.id_source=c.id OR a.id_source='') AND c.id_entite=:id_entite AND a.createBy=b.id AND (a.acces=0)";//                    
                }else{
                    $requete="SELECT a.*,a.id as id_document,a.createOn as createOnDoc,a.updateOn as updateOnDoc,CONCAT(nom,' ',prenom) AS utilisateur FROM document a, user b WHERE a.id_zone=:id_zone AND a.id_source=:id_mat AND a.createBy=b.id AND (a.acces=0)";//                    
                }
                $requete.=" ORDER BY ficName";	           
                //$requete="SELECT *, a.id as id_document FROM document a WHERE a.id_source=:id_mat";
                $prep=$db->prepare($requete);
                if ($_GET['affAll']==1){
                    $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT); 	                    
                }else{
                    $prep->bindParam(":id_mat",$_GET['id'],PDO::PARAM_INT);	
                    $prep->bindParam(":id_zone",$_SESSION['id_zone_org'],PDO::PARAM_INT);
                }
                $prep->execute();   
                while ($row = $prep->fetch(PDO::FETCH_ASSOC))
                {
                    $path = $GLOBALS['params']['appli']['document_folder'].'/files/'.$row[fic];                    
                    $extension=pathinfo($path,PATHINFO_EXTENSION);                    
                    //$finfo=finfo_open(FILEINFO_MIME_TYPE);
                    //$mm_type=finfo_file($finfo,$path);                      
                    //finfo_close($finfo);
                    $imginfo = getimagesize($path);
                    $filesize = taille($path);
                    $mysock = $imginfo;
                    if (in_array($extension,$GLOBALS['params']['appli']['extensionImgOk'])){
                        echo '<li>';
                        echo '<a class="thumb" name="'.$row[ficName].'" href="../scripts/affImage.php?file='.$row[fic].'&resize=1&size=500" title="'.$row[ficName].'">
                        <img src="../scripts/affImage.php?file='.$row[fic].'" alt="'.$row[ficName].'" style="max-height: 64px;max-width: 64px;" />
                        </a>';
                        echo '<div class="caption">
                            <div class="image-title">'.$row[ficName].'</div>
                            <div class="image-desc"><b>Envoyé par '.$row[utilisateur].' le '.$row[createOnDoc].'<br/>Modifié le '.$row[updateOnDoc].'</b></br><br/>Type : '.$imginfo[mime].'</br>Résolution : '.$imginfo[0].'x'.$imginfo[1].'<br/>Taille : '.$filesize.' '.$size_unit.'<br/>Desrciption : <br/>'.$row[description].'</div>
                            <div class="download">
                                <form method="post" action="../scripts/download.php">
                                    <input type="hidden" name="file" value="'.$row[fic].'">
                                    <input type="hidden" name="filename" value="'.$row[ficName].'">
                                    <input type="submit" value="Télécharger" style="cursor: pointer;background-color : #444; color: #DE7200;border: none;font-size: 12px;" title="">
                                </form>
                                <!--<a href="../scripts/download.php?file='.$row[fic].'&filename='.$row[ficName].'">Télécharger</a>-->
                            </div>
                        </div>';
                        echo '</li>';
                    }
                }
                ?>
                <!--
                <li>
                    <a class="thumb" name="leaf" href="http://farm4.static.flickr.com/3261/2538183196_8baf9a8015.jpg" title="Title #0">
                        <img src="http://farm4.static.flickr.com/3261/2538183196_8baf9a8015_s.jpg" alt="Title #0" />
                    </a>
                    <div class="caption">
                        <div class="image-title">Title #0</div>
                        <div class="image-desc">Description</div>
                        <div class="download">
                                <a href="http://farm4.static.flickr.com/3261/2538183196_8baf9a8015_b.jpg">Download Original</a>
                        </div>
                    </div>
                </li>-->
            </ul>
            <a class="pageLink next" style="visibility: hidden;" href="#" title="Next Page"></a>
        </div>
    </div>
    <div class="content">
        <div class="slideshow-container">
            <div id="controls" class="controls"></div>
            <div id="loading" class="loader"></div>
            <div id="slideshow" class="slideshow"></div>
        </div>
        <div id="caption" class="caption-container">
            <div class="photo-index"></div>
        </div>
    </div>
    <div style="clear: both;"></div>
    </div>  
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // We only want these styles applied when javascript is enabled
            $('div.content').css('display', 'block');

            // Initially set opacity on thumbs and add
            // additional styling for hover effect on thumbs
            var onMouseOutOpacity = 0.67;
            $('#thumbs ul.thumbs li, div.navigation a.pageLink').opacityrollover({
                mouseOutOpacity:   onMouseOutOpacity,
                mouseOverOpacity:  1.0,
                fadeSpeed:         'fast',
                exemptionSelector: '.selected'
            });

            // Initialize Advanced Galleriffic Gallery
            var gallery = $('#thumbs').galleriffic({
                delay:                     2500,
                numThumbs:                 10,
                preloadAhead:              10,
                enableTopPager:            false,
                enableBottomPager:         true,
                imageContainerSel:         '#slideshow',
                controlsContainerSel:      '#controls',
                captionContainerSel:       '#caption',
                loadingContainerSel:       '#loading',
                renderSSControls:          true,
                renderNavControls:         true,
                playLinkText:              'Lancer le diaporama',
                pauseLinkText:             'Mettre en pause',
                prevLinkText:              '&lsaquo; Précédent',
                nextLinkText:              'Suivant &rsaquo;',
                nextPageLinkText:          'Suiv &rsaquo;',
                prevPageLinkText:          '&lsaquo; Préc',
                enableHistory:             true,
                autoStart:                 false,
                syncTransitions:           true,
                defaultTransitionDuration: 900,
                onSlideChange:             function(prevIndex, nextIndex) {
                    // 'this' refers to the gallery, which is an extension of $('#thumbs')
                    this.find('ul.thumbs').children()
                            .eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
                            .eq(nextIndex).fadeTo('fast', 1.0);

                    // Update the photo index display
                    this.$captionContainer.find('div.photo-index')
                            .html('Photo '+ (nextIndex+1) +' sur '+ this.data.length);
                },
                onPageTransitionOut:       function(callback) {
                    this.fadeTo('fast', 0.0, callback);
                },
                onPageTransitionIn:        function() {
                    var prevPageLink = this.find('a.prev').css('visibility', 'hidden');
                    var nextPageLink = this.find('a.next').css('visibility', 'hidden');

                    // Show appropriate next / prev page links
                    if (this.displayedPage > 0)
                            prevPageLink.css('visibility', 'visible');

                    var lastPage = this.getNumPages() - 1;
                    if (this.displayedPage < lastPage)
                            nextPageLink.css('visibility', 'visible');

                    this.fadeTo('fast', 1.0);
                }
            });

            /**************** Event handlers for custom next / prev page links **********************/

            gallery.find('a.prev').click(function(e) {
                gallery.previousPage();
                e.preventDefault();
            });

            gallery.find('a.next').click(function(e) {
                gallery.nextPage();
                e.preventDefault();
            });

            /****************************************************************************************/

            /**** Functions to support integration of galleriffic with the jquery.history plugin ****/

            // PageLoad function
            // This function is called when:
            // 1. after calling $.historyInit();
            // 2. after calling $.historyLoad();
            // 3. after pushing "Go Back" button of a browser
            function pageload(hash) {
                // alert("pageload: " + hash);
                // hash doesn't contain the first # character.
                if(hash) {
                        $.galleriffic.gotoImage(hash);
                } else {
                        gallery.gotoIndex(0);
                }
            }

            // Initialize history plugin.
            // The callback is called at once by present location.hash. 
            $.historyInit(pageload, "advanced.html");

            // set onlick event for buttons using the jQuery 1.3 live method
            $("a[rel='history']").live('click', function(e) {
                if (e.button != 0) return true;

                var hash = this.href;
                hash = hash.replace(/^.*#/, '');

                // moves to a new page. 
                // pageload is called at once. 
                // hash don't contain "#", "?"
                $.historyLoad(hash);

                return false;
            });

            /****************************************************************************************/
        });
</script>
</body>
</html>