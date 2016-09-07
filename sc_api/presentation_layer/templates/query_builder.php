<script type="text/javascript" src="sc_api/presentation_layer/js/QueryBuilder.js"></script>

<h1>Query Builder</h1>
<hr>

<div class="content">
    <form method="GET" onSubmit="return process_submit();" id="queryBuilder" name="queryBuilder">
        <table style="width:100%;" CELLSPACING="0">
            <tr style="text-align:center;">

                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="api_source_td">
                        API Source<br>
                        <select name="api_source" id="api_source" onChange="rebuild_query_table(true);">
                            <option value="cache">Cache</option>
                            <option value="live">Live</option>
                        </select>
                    </div>
                </td>

                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="start_date_td">
                        Start Date (unix)<br>
                        <input type="text" name="start_date" id="start_date" onChange="rebuild_query_table();">
                    </div>
                </td>

                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="end_date_td">
                        End Date (unix)<br>
                        <input type="text" name="end_date" id="end_date" onChange="rebuild_query_table();">
                    </div>
                </td>

                <td style="width:25%;"></td>
            </tr>

            <tr style="text-align:center;">
                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="system_td">
                        System<br>
                        <select name="system" id="system" onChange="rebuild_query_table(true);">
                            <option value="accounts">Accounts</option>
                            <option value="organizations">Organizations</option>
                            <option value="forums">Forums</option>
                        </select>
                    </div>
                </td>

                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="action_td">
                        Action<br>
                        <select name="action" id="action" onChange="rebuild_query_table();">
                            <option></option>
                        </select>
                    </div>
                </td>

                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="source_td">
                        Data Source<br>
                        <select name="source" id="source" onChange="rebuild_query_table();">
                            <option value="rsi">RSI</option>
                            <option value="wikia">Wikia</option>
                        </select>
                    </div>
                </td>

                <td style="width:25%;"></td>
            </tr>

            <tr style="text-align:center;">
                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="target_td">
                        Target ID<br>
                        <input type="text" name="target_id" id="target" onChange="rebuild_query_table();">
                    </div>
                </td>

                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="start_td">
                        Start Page<br>
                        <input type="number" name="start_page" id="start" value="1" onChange="rebuild_query_table();">
                    </div>
                </td>

                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="end_td">
                        End Page<br>
                        <input type="number" name="end_page" id="end" value="1" onChange="rebuild_query_table();">
                    </div>
                </td>

                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="items_per_page_td">
                        Items Per Page<br>
                        <input type="number" name="items_per_page" id="items_per_page" value="255" onChange="rebuild_query_table();">
                    </div>
                </td>
            </tr>

            <tr style="text-align:center;">
                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="sort_method_td">
                        Sort Method<br>
                        <input type="text" name="sort_method" id="sort_method" onChange="rebuild_query_table();">
                    </div>
                </td>
                
                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="sort_direction_td">
                        Sort Direction<br>
                        <select name="sort_direction" id="sort_direction" onChange="rebuild_query_table(false);">
                            <option value="ascending">Ascending</option>
                            <option value="descending">Descending</option>
                        </select>
                    </div>
                </td>

                <td style="width:25%;"></td>

                <td style="width:25%;"></td>
            </tr>

            <tr style="text-align:center;">
                
                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="expedite_td">
                        Expedite<br>
                        <select name="expedite" id="expedite" onChange="rebuild_query_table(false);">
                            <option value="0" selected>False</option>
                            <option value="1">True</option>
                        </select>
                    </div>
                </td>
                
                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="location_td">
                        Output Location<br>
                        <select name="location" id="location" onChange="rebuild_query_table(false);">
                            <option value="inline">Inline</option>
                            <option value="redirect">Redirect</option>
                        </select>
                    </div>
                </td>
                
                <td style="width:25%;">
                    <div style="margin:0;padding:0;" id="format_td">
                        Output Format<br>
                        <select name="format" id="format" onChange="rebuild_query_table();">
                            <option value="pretty_json">Pretty JSON</option>
                            <option value="pretty_xml">Pretty XML</option>
                            <option value="json">JSON</option>
                            <option value="xml">XML</option>
                            <option value="raw">Raw</option>
                        </select>
                    </div>
                </td>

                <td style="width:25%;">
                </td>
            </tr>

            <tr>
                <td COLSPAN="99">
                    <input type="submit" id="submit" value="Submit Request" style="width:100%;">
                </td>
            </tr>

            <tr>
                <td COLSPAN="99">
                    Query String:<br>
                    <div id="query_string" style="margin:0;padding:0;background-color:rgb(100,100,100);font-size:80%;"</div>
                </td>
            </tr>

            <tr>
                <td COLSPAN="99" style="max-width:852px;">
                    <div style="margin:0;padding:0;display:none;" id="inline_td">
                        Returned Data:<br>
                        <textarea id="query_return" style="width:852px;overflow:auto;height:600px;"></textarea>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>

<script type="text/javascript">
rebuild_query_table(true);
</script>
