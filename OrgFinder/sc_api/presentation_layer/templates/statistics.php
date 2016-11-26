<?php 
    namespace thalos_api; 
    use PDO;
?>

<h1>API Statistics</h1>
<hr>

<?php

    require_once(__DIR__.'/../php/APIStatistics.php');

    function SmallestDenomination($time)
    {
        $seconds_in=array(
            0=>60,
            1=>60,
            2=>24,
            3=>30,
            4=>12);
        $names=array(
            -1=>'Second',
            0=>'Minute',
            1=>'Hour',
            2=>'Day',
            3=>'Month',
            4=>'Year');

        if($time<0)
        {
            return '0 seconds';
        }

        $i=0;
        while($time/$seconds_in[$i]>=1 && $seconds_in[$i]>0)
        {
            $time=$time/$seconds_in[$i];
            $i++;
        }

        return round($time, 2).' '.$names[$i-1].(round($time, 2)>1?'s':'');
    }

    $Stats = new APIStatistics();
?>

<div class="content">
    <table style="width:100%;text-align:center;">
        <tr>
            <td COLSPAN="99">
                <b>Overall</b>
            </td>
        </tr>
        <tr>
            <td class="rounded" COLSPAN="3">
                Unique Records<br>
                <span class="glowy_text">
                    <?php echo number_format($Stats->total_unique_records, 0, '.', ','); ?>
                </span>
                <br>
                <img src="sc_api/presentation_layer/images/tmp/unique_total.png" style="width:100%;">
            </td>
            <td class="rounded" COLSPAN="3">
                Total Records<br>
                <span class="glowy_text">
                    <?php echo number_format($Stats->total_org_count + $Stats->total_acct_count, 0, '.', ','); ?>
                </span>
                </span>
                <br>
                <img src="sc_api/presentation_layer/images/tmp/total_total.png" style="width:100%;">
            </td>
        </tr>
        <tr>
            <td class="rounded" COLSPAN="3">
                Current Cache Refresh Cycle<br>
                <span class="glowy_text">
                    <?php echo SmallestDenomination($Stats->current_refresh_cycle); ?>
                </span>
            </td>
            <td class="rounded" COLSPAN="3">
                Total API Hits<br>
                <span class="glowy_text">
                    <?php echo number_format($Stats->total_hits, 0, '.', ','); ?>
                </span>
            </td>
        </tr>
        <tr>
            <td class="rounded" COLSPAN="3">
                Unique Users<br>
                <span class="glowy_text">
                    <?php echo number_format($Stats->total_users, 0, '.', ','); ?>
                </span>
            </td>
            <td class="rounded" COLSPAN="3">
                <!--Cache/Live Hit Ratio<br>
                <span class="glowy_text">

                </span>-->
            </td>
        </tr>

        <tr style="background-color:transparent;">
            <td COLSPAN="99" style="background-color:transparent;">
                &nbsp;
            </td>
        </tr>

        <tr>
            <td COLSPAN="2">
                <b>Organizations</b>
            </td>

            <td style="background-color:transparent;">
                &nbsp;
            </td>

            <td style="background-color:transparent;">
                &nbsp;
            </td>

            <td COLSPAN="2">
                <b>Accounts</b>
            </td>
        </tr>
        <tr>
            <td class="rounded">
                Unique Records<br>
                <span class="glowy_text">
                    <?php echo number_format($Stats->unique_org_count, 0, '.', ','); ?>
                </span>
                <br>
                <img src="sc_api/presentation_layer/images/tmp/unique_orgs.png" style="width:100%;">
            </td>
            <td class="rounded">
                Total Records<br>
                <span class="glowy_text">
                    <?php echo number_format($Stats->total_org_count, 0, '.', ','); ?>
                </span>
                <br>
                <img src="sc_api/presentation_layer/images/tmp/total_orgs.png" style="width:100%;">
            </td>

            <td style="background-color:transparent;">
                &nbsp;
            </td>

            <td style="background-color:transparent;">
                &nbsp;
            </td>

            <td class="rounded">
                Unique Records<br>
                <span class="glowy_text">
                    <?php echo number_format($Stats->unique_acct_count, 0, '.', ','); ?>
                </span>
                <br>
                <img src="sc_api/presentation_layer/images/tmp/unique_accts.png" style="width:100%;">
            </td>
            <td class="rounded">
                Total Records<br>
                <span class="glowy_text">
                    <?php echo number_format($Stats->total_acct_count, 0, '.', ','); ?>
                </span>
                <br>
                <img src="sc_api/presentation_layer/images/tmp/total_accts.png" style="width:100%;">
            </td>
        </tr>
        <tr>
            <td class="rounded">
                <!--Total API Hits<br>
                <span class="glowy_text">
                </span>-->
            </td>
            <td class="rounded">
                <!--Cache/Live Hit Ratio<br>
                <span class="glowy_text">
                </span>-->
            </td>

            <td style="background-color:transparent;">
                &nbsp;
            </td>

            <td style="background-color:transparent;">
                &nbsp;
            </td>

            <td class="rounded">
                <!--Total API Hits<br>
                <span class="glowy_text">
                </span>-->
            </td>
            <td class="rounded">
                <!--Cache/Live Hit Ratio<br>
                <span class="glowy_text">
                </span>-->
            </td>
        </tr>
    </table>   
</div>