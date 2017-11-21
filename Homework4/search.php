<?php
// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');
$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;

if ($query)
{
    // The Apache Solr Client library should be on the include path
    // which is usually most easily accomplished by placing in the
    // same directory as this script ( . or current directory is a default
    // php include path entry in the php.ini)
    require_once('Apache/Solr/Service.php');
    // create a new solr service instance - host, port, and webapp
    // path (all defaults in this example)
    $solr = new Apache_Solr_Service('localhost', 8983, '/solr/boston_globe/');
    // if magic quotes is enabled then stripslashes will be needed
    if (get_magic_quotes_gpc() == 1)
    {
        $query = stripslashes($query);
    }
    // in production code you'll always want to use a try /catch for any
    // possible exceptions emitted  by searching (i.e. connection
    // problems or a query parsing error)
    try
    {
        $results = $solr->search($query, 0, $limit);
        $prresults = $solr->search($query, 0, $limit, array('sort'=>'pageRankFile desc'));

    }
    catch (Exception $e)
    {
        // in production you'd probably log or email this error to an admin
        // and then show a special message to the user but for this example
        // we're going to show the full exception
        die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
    }
}
?>
<html>
<head>
    <title>NOOB Search</title>
    <style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
}

/* Create two unequal columns that floats next to each other */
.column {
    float: left;
    padding: 10px; /* Should be removed. Only for demonstration */
}

.left {
  width: 50%;
  word-wrap: break-word; 
}

.right {
  width: 50%;
  word-wrap: break-word; 
word-break: normal; 
}

/* Clear floats after the columns */
.row:after {
    content: "";
    display: table;
    clear: both;
}
</style>
</head>
<body>
    <br>
    <br>
<form  accept-charset="utf-8" method="get">
    <label for="q">Search:</label>
    <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
    <input type="submit"/>
</form>
<div class="display">
    <div class="column left">
<?php
// display results
if ($results)
{
    $total = (int) $results->response->numFound;
    $start = min(1, $total);
    $end = min($limit, $total);
    ?>
    <h2>Lucene Ranking</h2>
    <div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
    <ol>
        <?php
        $docs0 = $results->response->docs;
        // iterate result documents
        for($i = 0;$i < 10; ++$i)
        {
            ?>
            <li>
                <table>
                    <tr>
                        <?php
                        if($i < sizeof($docs0))
                        {
                            $doc = $docs0[$i];
                            $link = $doc->og_url;
                            
                            ?>
                            <tr>
                                <qw><?php echo "<a href = '{$link}' STYLE='text-decoration:none'><font size='4px'><b>".$doc->title."</b></font></a>" ?></qw>
                                <br>
                            </tr>
                            <tr>
                                <qw>link:</qw>
                                <qw><?php echo "<a href = '{$link}' STYLE='text-decoration:none'><st>".$link."</st></a>" ?></qw>
                                <br>
                            </tr>
                            <tr>
                                <qw>id:</qw>
                                <qw><?php echo htmlspecialchars($doc->id, ENT_NOQUOTES, 'utf-8'); ?></qw>
                                <br>
                            </tr>
                            <tr>
                                <qw>description:</qw>
                                <qw><?php echo htmlspecialchars($doc->description, ENT_NOQUOTES, 'utf-8'); ?></qw>
                                <br>
                            </tr>
                        <?php
                        }
                        ?>
                    </tr>
                </table>
            </li>
            <?php
        }
        ?>
    </ol>

    <?php
}
?>
</div>
<div class="column right">
<?php
// display results
if ($prresults)
{
    $total = (int) $prresults->response->numFound;
    $start = min(1, $total);
    $end = min($limit, $total);
    ?>
    <h2>PageRank Ranking</h2>
    <div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
    <ol>
        <?php
        $docs0 = $prresults->response->docs;
        // iterate result documents
        for($i = 0;$i < 10; ++$i)
        {
            ?>
            <li>
                <table>
                    <tr>
                        <?php
                        if($i < sizeof($docs0))
                        {
                            $doc = $docs0[$i];
                            $link = $doc->og_url;
                            ?>
                            <tr>
                                <qw><?php echo "<a href = '{$link}' STYLE='text-decoration:none'><font size='4px'><b>".$doc->title."</b></font></a>" ?></qw>
                                <br>
                            </tr>
                            <tr>
                                <qw>link:</qw>
                                <qw><?php echo "<a href = '{$link}' STYLE='text-decoration:none'><st>".$link."</st></a>" ?></qw>
                                <br>
                            </tr>
                            <tr>
                                <qw>id:</qw>
                                <qw><?php echo htmlspecialchars($doc->id, ENT_NOQUOTES, 'utf-8'); ?></qw>
                                <br>
                            </tr>
                            <tr>
                                <qw>description:</qw>
                                <qw><?php echo htmlspecialchars($doc->description, ENT_NOQUOTES, 'utf-8'); ?></qw>
                                <br>
                            </tr>
                        <?php
                        }
                        ?>
                    </tr>
                </table>
            </li>
            <?php
        }
        ?>
    </ol>
    <?php
}
?>
</div>
</div>
</body>
</html>