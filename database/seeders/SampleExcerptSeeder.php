<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class SampleExcerptSeeder extends Seeder
{
    /**
     * Seed original sample chapter excerpts for the 'Look Inside' reader modal.
     */
    public function run(): void
    {
        $sampleTexts = [
            "Chapter One: The Horizon Line\n\nThe dawn arrived not with sudden brilliance, but with a cautious gray wash across the eastern dunes. Julian stood by the weathered parapet, feeling the morning chill settle deep into his wool coat. In the valley below, the silent rooftops of the old quarter were beginning to catch the first hints of amber light.\n\nHe opened the brass pocket watch his grandfather had carried through three campaigns. The ticking was rhythmic, almost defiant against the vast quiet of the plains. Somewhere beneath the stones of this ancient city, secrets had rested undisturbed for seven generations—and today, the excavation would finally reach the inner vault.\n\n\"We are ready, sir,\" a quiet voice broke the stillness behind him. Julian closed the watch with a soft click and turned toward the arched doorway.",

            "Chapter One: The Library of Solitude\n\nThere is a peculiar stillness that belongs only to high-vaulted rooms lined with leather bindings. When the afternoon rain taps against leaded glass, time seems to fold in upon itself, erasing the distinction between centuries.\n\nMiriam adjusted the brass reading lamp and turned the fragile parchment page. The handwriting was neat, sloping with the deliberate cadence of a medieval scribe who knew his work was meant to outlive empires. She traced the illuminated initial letter—a serpentine azure flourish wrapped in delicate silver leaf.\n\nIf the translation held true, the chronicler had witnessed the vanishing of the northern lighthouse not during a tempest, but on a night of absolute, starry calm.",

            "Chapter One: The First Crossing\n\nThe salt spray struck the bow like handfuls of cold gravel. Captain Vane gripped the spoke of the wheel, his knuckles pale against dark oak. Three miles astern, the headland lanterns of Port Alden were swallowed by fog.\n\nMost navigators avoided the strait after the autumn equinox, warning of shifting sandbanks and currents that could draw a three-masted brig toward the reef in minutes. But Vane carried a sealed packet bearing the harbor master's urgent wax seal, and the tides waited for neither caution nor prayer.\n\n\"Look ahead to larboard!\" shouted the lookout from the shrouds. Through the veil of mist, a faint, rhythmic pulse of amber glowed where no beacon had ever been charted."
        ];

        $books = Book::all();
        foreach ($books as $index => $book) {
            $excerpt = $sampleTexts[$index % count($sampleTexts)];
            $book->update(['sample_excerpt' => $excerpt]);
        }
    }
}
