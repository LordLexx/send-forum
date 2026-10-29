<?php
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\NewsletterSubscriber;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithFileUploads;

    public $csv_file = null;
    public $importResults = null;
    public $isImporting = false;

    public function import()
    {
        $this->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048', // Max 2MB
        ]);

        $this->isImporting = true;
        $this->importResults = null;

        $path = $this->csv_file->getRealPath();
        $file = fopen($path, 'r');

        $importedCount = 0;
        $skippedCount = 0;

        // Read through the CSV file row by row
        while (($row = fgetcsv($file)) !== false) {
            if (empty($row[0])) continue;
            
            $email = trim($row[0]);

            // Basic email validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            // firstOrCreate will insert it if it doesn't exist, or skip if it does.
            // We set is_verified = true since the admin is manually importing them.
            $subscriber = NewsletterSubscriber::firstOrCreate(
                ['email' => $email],
                [
                    'is_verified' => true,
                    'verification_token' => null,
                ]
            );

            if ($subscriber->wasRecentlyCreated) {
                $importedCount++;
            } else {
                $skippedCount++;
            }
        }
        fclose($file);

        $this->isImporting = false;
        $this->csv_file = null;
        
        $this->importResults = [
            'imported' => $importedCount,
            'skipped' => $skippedCount
        ];

        $this->dispatch('notify', type: 'success', message: "Import complete! Added {$importedCount} new subscribers, skipped {$skippedCount} existing.");
    }
};
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Import Subscribers</h2>
            <p class="text-sm text-gray-500 mt-1">Upload a CSV file to bulk-add verified email addresses.</p>
        </div>
        <a href="{{ route('admin.newsletters.subscribers') }}" class="inline-flex items-center px-4 py-2.5 border border-gray-200 text-gray-700 bg-white rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Subscribers
        </a>
    </div>

    <!-- Import Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-2xl mx-auto">
        
        @if ($importResults)
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center justify-between">
                <div>
                    <strong>Success!</strong> Added {{ $importResults['imported'] }} new subscribers. 
                    Skipped {{ $importResults['skipped'] }} duplicates.
                </div>
                <button wire:click="$set('importResults', null)" class="text-green-500 hover:text-green-700">&times;</button>
            </div>
        @endif

        <form wire:submit="import" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
                <p class="text-xs text-gray-500 mb-3">The file must have one email address per row. The first column should contain the email. Headers are automatically ignored if they aren't valid emails.</p>
                
                <div class="flex items-center justify-center w-full">
                    <label for="csv_file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            @if ($csv_file)
                                <svg class="w-8 h-8 mb-3 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <p class="text-sm text-gray-700 font-medium">{{ $csv_file->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-500">Click to change file</p>
                            @else
                                <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-400">CSV files only (MAX 2MB)</p>
                            @endif
                        </div>
                        <input id="csv_file" type="file" wire:model="csv_file" accept=".csv,.txt" class="hidden" />
                    </label>
                </div>
                @error('csv_file') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end space-x-3">
                <button type="submit" wire:loading.attr="disabled" wire:target="import" class="inline-flex items-center px-4 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg wire:loading.remove wire:target="import" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    <svg wire:loading wire:target="import" class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="import">Process Import</span>
                    <span wire:loading wire:target="import">Importing...</span>
                </button>
            </div>
        </form>
    </div>
</div>