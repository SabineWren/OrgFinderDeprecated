
function rebuild_query_table(reset_actions)
{
    reset_actions = (typeof reset_actions === "undefined") ? false : reset_actions;
    
    var api_source = document.getElementById('api_source');
    var end_date = document.getElementById('end_date');
    var start_date = document.getElementById('start_date');
    var system = document.getElementById('system');
    var action = document.getElementById('action');
    var source = document.getElementById('source');
    var target = document.getElementById('target');
    var start = document.getElementById('start');
    var end = document.getElementById('end');
    var items_per_page = document.getElementById('items_per_page');
    var format = document.getElementById('format');
    var location = document.getElementById('location');
    var sort_method = document.getElementById('sort_method');
    var sort_direction = document.getElementById('sort_direction');
    var expedite = document.getElementById('expedite');

    var system_td = document.getElementById('system_td');
    var action_td = document.getElementById('action_td');
    var source_td = document.getElementById('source_td');
    var target_td = document.getElementById('target_td');
    var start_td = document.getElementById('start_td');
    var end_td = document.getElementById('end_td');
    var items_per_page_td = document.getElementById('items_per_page_td');
    var format_td = document.getElementById('format_td');
    var location_td = document.getElementById('location_td');
    var api_source_td = document.getElementById('api_source_td');
    var end_date_td = document.getElementById('end_date_td');
    var start_date_td = document.getElementById('start_date_td');
    var sort_method_td = document.getElementById('sort_method_td');
    var sort_direction_td = document.getElementById('sort_direction_td');
    var expedite_td = document.getElementById('expedite_td');

    action_td.style.display = 'none';
    source_td.style.display = 'none';
    target_td.style.display = 'none';
    start_td.style.display = 'none';
    end_td.style.display = 'none';
    start_date_td.style.display = 'none';
    end_date_td.style.display = 'none';
    items_per_page_td.style.display = 'none';
    sort_method_td.style.display = 'none';
    sort_direction_td.style.display = 'none';
    expedite_td.style.display = 'none';

    action.hidden = true;
    source.hidden = true;
    target.hidden = true;
    start.hidden = true;
    end.hidden = true;
    start_date.hidden = true;
    end_date.hidden = true;
    items_per_page.hidden = true;
    sort_method.hidden = true;
    sort_direction.hidden = true;
    expedite.hidden = true;

    if(reset_actions)
    {
        while (action.options.length)
        {
            action.remove(0);
        }
    }
    
    switch(api_source.value)
    {
        case 'cache':
            start_date_td.style.display = 'block';
            end_date_td.style.display = 'block';
            start_date.hidden = false;
            end_date.hidden = false;
            break;
        case 'live':
            expedite_td.style.display = 'block';
            expedite.hidden = false;
            break;
    }

    switch(system.value)
    {
        case 'organizations':

            if(reset_actions)
            {
                action.options.add(new Option("All Organizations", "all_organizations"));
                action.options.add(new Option("Single Organization", "single_organization"));
                action.options.add(new Option("Organization Members", "organization_members"));
            }

            action_td.style.display = 'block';
            action.hidden = false;

            switch(action.value)
            {
                case 'all_organizations':
                    source_td.style.display = 'block';
                    source.hidden = false;

                    switch(source.value)
                    {
                        case 'rsi':
                            start_td.style.display = 'block';
                            end_td.style.display = 'block';
                            items_per_page_td.style.display = 'block';
                            sort_method_td.style.display = 'block';
                            sort_direction_td.style.display = 'block';
                            start.hidden = false;
                            end.hidden = false;
                            items_per_page.hidden = false;
                            sort_method.hidden = false;
                            sort_direction.hidden = false;
                            break;

                        case 'wikia':
                            break;
                    }
                    break;

                case 'single_organization':
                    target_td.style.display = 'block';
                    target.hidden = false;
                    break;

                case 'organization_members':
                    target_td.style.display = 'block';
                    start_td.style.display = 'block';
                    end_td.style.display = 'block';
                    target.hidden = false;
                    start.hidden = false;
                    end.hidden = false;
                    break;
            }
            break;
        case 'accounts':

            switch(api_source.value)
            {
                case 'cache':
                    if(reset_actions)
                    {
                        action.options.add(new Option("Full Profile", "full_profile"));
                        action.options.add(new Option("All Accounts", "all_accounts"));
                    }
                    break;
                case 'live':
                    if(reset_actions)
                    {
                        action.options.add(new Option("Full Profile", "full_profile"));
                        action.options.add(new Option("Dossier", "dossier"));
                        action.options.add(new Option("Forum Profile", "forum_profile"));
                        action.options.add(new Option("Threads", "threads"));
                        action.options.add(new Option("Posts", "posts"));
                        action.options.add(new Option("Memberships", "memberships"));
                    }
                    break;
            }

            switch(action.value)
            {
                case 'posts':
                case 'threads':
                    start_td.style.display = 'block';
                    end_td.style.display = 'block';
                    start.hidden = false;
                    end.hidden = false;
                    break;
            }

            action_td.style.display = 'block';
            target_td.style.display = 'block';
            action.hidden = false;
            target.hidden = false;
            break;
        case 'forums':
            if(reset_actions)
            {
                action.options.add(new Option("Posts", "posts"));
                action.options.add(new Option("Threads", "threads"));
                action.options.add(new Option("Forums", "forums"));
            }

            action_td.style.display = 'block';
            action.hidden = false;

            switch(action.value)
            {
                default:
                    target_td.style.display = 'block';
                    start_td.style.display = 'block';
                    end_td.style.display = 'block';
                    target.hidden = false;
                    start.hidden = false;
                    end.hidden = false;
                    break;
                case 'forums':
                    break;
            }
            break;
    }

    document.getElementById('query_string').innerHTML=build_query_string(document.URL);
}

function process_submit()
{
    var location = document.getElementById('location').value;

    switch(location)
    {
        case 'inline':
            inline_query();
            return false;
            break;
        case 'redirect':
            return true;
            break;
    }
}

function build_query_string(base)
{
    var controls = document.queryBuilder.elements;
    
    var url = base + '?';
    
    for(var i = 0; i < controls.length; i++) 
    {
        if(controls[i].style.display != 'none'
            && controls[i].hidden == false
            && controls[i].type != 'submit'
            && controls[i].type != 'textarea'
            && controls[i].name != 'location')
        {
            if(i > 0)
            {
                url += '&';
            }
            
            url += controls[i].name + '=' + escape(controls[i].value);
        }
    }

    return url;
}

function inline_query()
{
    var location = document.getElementById('location').value;

    document.getElementById("inline_td").style.display = 'none';

    document.getElementById("submit").value = 'Processing...';

    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById("query_return").innerHTML = xmlhttp.responseText;
            document.getElementById("inline_td").style.display = 'block';

            document.getElementById("submit").value = 'Submit Request';
        }
    }

    var url=build_query_string('index.php') + '&location='+escape(location);

    xmlhttp.open("GET",url,true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send();
}