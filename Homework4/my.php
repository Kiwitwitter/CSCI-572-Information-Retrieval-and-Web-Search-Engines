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
        $sortedresults = $solr->search($query, 0, $limit, array('sort'=>'pageRankFile desc'));

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
</head>
<body>
<form  accept-charset="utf-8" method="get">
    <label for="q">Search:</label>
    <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
    <input type="submit"/>
</form>
<?php
// display results
if ($results)
{
    $total = (int) $results->response->numFound;
    $start = min(1, $total);
    $end = min($limit, $total);
    ?>
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
                        <td width="50%" valign="top">
                            <table width="100%" style="border: 1px solid black; text-align: left">
                                <tr style="text-align: center; background: grey; color: white;">
                                    <td colspan="2"><b>Internal Ranking</b></td>
                                </tr>
                                <?php
                                // iterate document fields / values
                                if($i < sizeof($docs0))
                                {
                                    $doc = $docs0[$i];
                                    $link = $doc->og_url;
                                    ?>
                                    <tr>
                                        <th>title</th>
                                        <td><?php echo "<a href = '{$link}' STYLE='text-decoration:none'><font size='4px'><b>".$doc->title."</b></font></a>" ?></td>
                                    </tr>
                                    <tr>
                                        <th>link</th>
                                        <td><?php echo "<a href = '{$link}' STYLE='text-decoration:none'><st>".$link."</st></a>" ?></td>
                                    </tr>
                                    <tr>
                                        <th>id</th>
                                        <td><?php echo htmlspecialchars($doc->id, ENT_NOQUOTES, 'utf-8'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>description</th>
                                        <td><?php echo htmlspecialchars($doc->description, ENT_NOQUOTES, 'utf-8'); ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </td>

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
</body>
</html>