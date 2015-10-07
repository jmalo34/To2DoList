<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";

    session_start();

    //if there isn't a list of tasks, or that so-named variable "list_of_tasks" has no value, set it to an (empty, for now) array
    if (empty($_SESSION['list_of_tasks']))
    {
        $_SESSION['list_of_tasks'] = array();
    }

    $app = new Silex\Application();
    $app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../views'));

    //"use ($app)" gives our route access to the app variable
    $app->get("/", function() use ($app)
    {
        //the only thing this route needs to do is get the data needed for use in our template and pass it in by rendering the template
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    //route for URL at '/tasks'; pass $app variable into route with the keyword 'use'
    $app->post("/tasks", function() use ($app)
    {
        //since form action was set to POST method, data will be read out of the form by using the superglobal $_POST variable. Here, we instatiate an instance of Task.
        $task = new Task($_POST['description']);
        //pass(?) THIS instance of Task into the save method from Task class
        $task->save();
        //return text containing the task description, + also, a link to go see a list of all the tasks (located at home, AKA '/')
        return $app['twig']->render('create_task.html.twig');
    });

    $app->post("delete_tasks", function()
    {
        Task::deleteAll();

        return "
            <h1>List Cleared!</h1>
            <p><a href='/'>Home</a></p>
        ";
    });

    return $app;
 ?>
