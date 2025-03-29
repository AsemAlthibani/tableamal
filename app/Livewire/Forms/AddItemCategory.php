<?php

namespace App\Livewire\Forms;

use App\Models\ItemCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class AddItemCategory extends Component
{
    use LivewireAlert;

    public $categoryName = '';
    public $translations = [];
    public $languages = [];
    public $currentLanguage;

    public function mount()
    {
        $this->languages = languages()->pluck('language_name', 'language_code')->toArray();
        $this->translations = array_fill_keys(array_keys($this->languages), '');
        $this->currentLanguage = global_setting()->locale;
    }

    public function updateTranslation()
    {
        $this->translations[$this->currentLanguage] = $this->categoryName;
    }

    public function updatedCurrentLanguage()
    {
        $this->categoryName = $this->translations[$this->currentLanguage] ?? '';
    }

    public function submitForm()
    {
        $this->validate([
            'translations.en' => 'required|unique:item_categories,category_name,null,id,branch_id,' . branch()->id,
        ], [
            'translations.en.required' => __('validation.categoryNameRequired'),
        ]);

        $filteredTranslations = array_filter($this->translations, 'trim');

        ItemCategory::create([
            'category_name' => $filteredTranslations
        ]);

        // Reset the value
        $this->categoryName = '';
        $this->translations = array_fill_keys(array_keys($this->translations), '');

        $this->dispatch('refreshCategories');
        $this->dispatch('hideCategoryModal');

        $this->alert('success', __('messages.categoryAdded'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.add-item-category');
    }

}
