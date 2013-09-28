<?php
function beforePageExecute() 
{
    if (DEVELOPER_MODE) {
        $qC = PDODebug::$queriesCount;
        $eT = number_format((microtime(true) - STARTED_AT), 4);
        Process::$context['debug_toolbar_body'] = "
        <span style='color:lightgray;'>Tranquility Dev</span> | Time: <div id='debug_execution_time' style='display: inline;'>{$eT}ms</div> | SQL Queries: $qC";
    } else {
        Process::$context['debug_toolbar_body'] = false;
    }
}

