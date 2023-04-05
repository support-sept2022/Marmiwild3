<?php

require __DIR__ . '/../models/RecipeModel.php';


class RecipeController
{
    private $model;

    public function __construct()
    {
        $this->model = new RecipeModel();
    }

    public function browse(): void
    {
        $recipes = $this->model->getAll();

        require __DIR__ . '/../views/index.php';
    }

    public function show(int $id): void
    {
        // Input parameter validation (integer >0)
        $id = filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        if (false === $id || null === $id) {
            header("Location: /");
            exit("Wrong input parameter");
        }

        // Fetching a recipe
        //$recipe = getRecipeById($id);

        $recipe = $this->model->getById($id);

        // Result check
        if (!isset($recipe['title']) || !isset($recipe['description'])) {
            header("Location: /");
            exit("Recipe not found");
        }

        // Generate the web page
        require __DIR__ . '/../views/show.php';
    }

    public function add(): void
    {
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $recipe = array_map('trim', $_POST);

            // Validate data
            $errors = $this->validate($recipe);

            // Save the recipe
            if (empty($errors)) {
                $this->model->save($recipe);
                header('Location: /');
            }
        }

        // Generate the web page
        require __DIR__ . '/../views/form.php';
    }

    public function update(int $id): void
    {
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $recipe = array_map('trim', $_POST);

            // Validate data
            $errors = $this->validate($recipe);

            // Save the recipe
            if (empty($errors)) {
                $this->model->update($recipe, $id);
                header('Location: /show?id=' . $id);
            }
        }

        // Generate the web page
        require __DIR__ . '/../views/form.php';
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        header('Location: /');
    }

    public function validate(array $recipe): array
    {
        if (empty($recipe['title'])) {
            $errors[] = 'The title is required';
        }
        if (empty($recipe['description'])) {
            $errors[] = 'The description is required';
        }
        if (!empty($recipe['title']) && strlen($recipe['title']) > 255) {
            $errors[] = 'The title should be less than 255 characters';
        }

        return $errors ?? [];
    }
}