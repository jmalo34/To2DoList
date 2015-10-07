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
        //begin with an empty output string
        $output = "";

        //creating the $all_tasks variable -- to be equal to the ouput of the getAll method -- means we only have to be calling the getAll method once because here now we can use the $all_tasks variable to: *1) check if any tasks exist, and then 1a) loop through any tasks, and print their descriptions
        $all_tasks = Task::getAll();

        //*
        if (!empty($all_tasks))
        {
            $output = $output . "
                <h1>To 2 Do List</h1>
                <p>Here are all your tasks:</p>
                ";

            //loop through Tasks stored in session and print descriptions
            foreach ($all_tasks as $task)
            {
                $output = $output . "<p>" . $task->getDescription() . "</p>";
            }
        }

        //display form that when submitted, will create a new instance of the Task class. Notice the form method is set to "post".
        $output = $output . "
            <form action='/tasks' method='post'>
                <label for='description'>Task Description</label>
                <input id='description' name='description' type='text'>

                <button type='submit'>Add task</button>
            </form>
        ";

        //button to clear list of Tasks
        $output .= "
            <form action='delete_tasks' method='post'>
                <button type='submit'>clear list</button>
            </form>
        ";

        //tell $app object to user Twig to render a file called tasks.html.twig. this file will display the list of tasks along with the form to create new task and/or clear list. return value from getAll method is assigned to a variable named tasks, and is available to use inside the template file. More simply: Passing a variable named 'tasks' into our Twig template ('tasks' holds an array of all our Task objects, as returned by static getAll method)
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    //route for URL at '/tasks'
    $app->post("/tasks", function()
    {
        //since form action was set to POST method, data will be read out of the form by using the superglobal $_POST variable. Here, we instatiate an instance of Task.
        $task = new Task($_POST['description']);
        //pass(?) THIS instance of Task into the save method from Task class
        $task->save();
        //return text containing the task description, + also, a link to go see a list of all the tasks (located at home, AKA '/')
        return
            "
            <h1>You created a task!</h1>
            <p>" . $task->getDescription() . "</p>
            <p><a href='/'>View your list of things to do.</a></p>
            ";
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
