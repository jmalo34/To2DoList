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

        //loop through Tasks stored in session and print descriptions
        foreach (Task::getAll() as $task)
        {
            $output = $output . "<p>" . $task->getDescription() . "</p>";
        }

        //display form that when submitted, will create a new instance of the Task class. Notice the form method is set to "post".
        $output = $output . "
            <form action='/tasks' method='post'>
                <label for='description'>Task Description</label>
                <input id='description' name='description' type='text'>

                <button type='submit'>Add task</button>
            </form>
            ";

        return $output;
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
    })

    return $app;
 ?>
