<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";

    session_start();
    if (empty($_SESSION['list_of_tasks']))
    {
        $_SESSION['list_of_tasks'] = array();
    }

    $app = new Silex\Application();

    $app->get("/", function()
    {
        $output = "";

        foreach (Task::getAll() as $task)
        {
            $output = $output . "<p>" . $task->getDescription() . "</p>";
        }

        return $output;
    });

    return $app;
 ?>
