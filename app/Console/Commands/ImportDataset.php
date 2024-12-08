<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;
use App\Models\Flashcard;
use App\Models\FlashcardSet;

class ImportDataset extends Command
{
    protected $signature = 'dataset:import';
    protected $description = 'Import flashcards and sets from openapi.yaml';

    public function handle()
    {
        $path = storage_path('datasets/openapi.yaml');

        // Check if the file exists
        if (!file_exists($path)) {
            $this->error("Dataset not found at $path");
            return;
        }

        try {
            // Parse the YAML file
            $data = Yaml::parseFile($path);
        } catch (\Exception $e) {
            $this->error("Failed to parse YAML file: " . $e->getMessage());
            return;
        }

        $this->info("Parsing dataset...");

        // Extract the schemas section
        $schemas = $data['components']['schemas'] ?? [];

        // Import FlashcardSet if example exists
        if (isset($schemas['FlashcardSet']['properties'])) {
            $exampleSet = $schemas['FlashcardSet']['properties'];

            // Extract example values
            $name = $exampleSet['name']['example'] ?? 'Default Set';
            $user_id = $exampleSet['user_id']['example'] ?? 1;
            $rating = $exampleSet['rating']['example'] ?? 0;

            // Insert FlashcardSet into the database
            $flashcardSet = FlashcardSet::create([
                'name' => $name,
                'user_id' => $user_id,
                'rating' => $rating,
            ]);

            $this->info("Imported Flashcard Set: $name");
        } else {
            $this->error("No example found for FlashcardSet.");
            return;
        }

        // Import FlashCard if example exists
        if (isset($schemas['FlashCard']['properties'])) {
            $exampleCard = $schemas['FlashCard']['properties'];

            // Extract example values
            $question = $exampleCard['question']['example'] ?? 'Default Question';
            $answer = $exampleCard['answer']['example'] ?? 'Default Answer';
            $is_hidden = $exampleCard['is_hidden']['example'] ?? false;

            // Insert FlashCard into the database
            Flashcard::create([
                'question' => $question,
                'answer' => $answer,
                'set_id' => $flashcardSet->id,
                'is_hidden' => $is_hidden,
            ]);

            $this->info("Imported Flashcard: $question");
        } else {
            $this->error("No example found for FlashCard.");
            return;
        }

        $this->info("Dataset import completed successfully.");
    }
}
