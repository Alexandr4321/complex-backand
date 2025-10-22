<?php

namespace App\Base\Locale;

use App\Base\Models\Locale;
use App\Base\Models\Translation;
use App\Base\Models\TranslationCustom;
use App\System\Exceptions\ServerException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;

trait Localized
{
//    public const localized = [];
//    public const localizedTable = false; // or true or '_localized'
    
    protected $disableLocalized = false;
    protected $localeId = null; // Locale::$id
    
    public function scopeLocalized($query, $localeId = null)
    {
        $this->localeId = $localeId;
        return $query->with('translations');
    }
    
    public function localized($localeId = null)
    {
        $this->localeId = $localeId;
        $this->load('translations');
        return $this;
    }
    
    
    public function translations()
    {
        return $this->translationsAll()->where('localeId', $this->localeId ?: LocaleService::current()->id);
    }
    
    public function translationsAll()
    {
        if (defined(get_class($this).'::localizedTable') && $this::localizedTable) {
            $table = LocaleService::getTranslationsTable($this);
            $query = new Builder(new QueryBuilder($this->getConnection()));
            $query->setModel(new TranslationCustom());
            $query = $query->from($table)->select('*');
            return $this->newHasMany($query, $this, $table.'.'.'id', 'id');
        }
        return $this->morphMany(Translation::class, null, 'ownerType', 'ownerId');
    }
    
    /**
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = parent::getAttributes();
        if (!$this->disableLocalized && count($this::localized)) {
            $translations = Arr::get($this->relations, 'translations');
            if ($translations && count($translations)) {
                if (defined(get_class($this).'::localizedTable') && $this::localizedTable) {
                    $translation = Arr::get($translations, 0, new TranslationCustom());
                    foreach ($this::localized as $fieldName) {
                        $attributes[$fieldName] = $translation->{$fieldName};
                    }
                } else {
                    $translations = $translations->pluck('value', 'field');
                    foreach ($this::localized as $fieldName) {
                        $attributes[$fieldName] = Arr::get($translations, $fieldName, null);
                    }
                }
            }
        }
        return $attributes;
    }
    
    protected static function booted()
    {
        parent::booted();
        static::deleting(function ($model) {
            $model->translationsAll()->delete();
        });
    }
    
    public function save(array $options = [])
    {
        $fieldsToSave = [];
        foreach ($this::localized as $fieldName) {
            $value = Arr::get($this->attributes, $fieldName, new MissingValue());
            if (is_object($value) && is_a($value, MissingValue::class)) {
                continue;
            }
            $fieldsToSave[$fieldName] = $value;
            unset($this->attributes[$fieldName]);
        }
        $this->disableLocalized = true;
        parent::save($options);
        $this->disableLocalized = false;
        
        if ($this::localized) {
            $locales = Locale::get()->keyBy('name');
            
            if (defined(get_class($this).'::localizedTable') && $this::localizedTable) {
                $translations = $this->translationsAll->keyBy('localeId');
                foreach ($fieldsToSave as $fieldName => $value) {
                    if (is_array($value)) {
                        foreach ($value as $localeName => $v) {
                            $locale = Arr::get($locales, $localeName);
                            if (!$locale) {
                                throw new ServerException('Locale with name '.$localeName.' not found');
                            }
                            $this->createCustomTranslation($translations, $locale, $fieldName, $v);
                        }
                    } else {
                        $locale = LocaleService::current();
                        $this->createCustomTranslation($translations, $locale, $fieldName, $value);
                    }
                    unset($this->attributes[$fieldName]);
                }
                foreach ($translations as $translation) {
                    $translation->setTable(LocaleService::getTranslationsTable($this));
                    $translation->save();
                }
            } else {
                $translations = $this->translationsAll->keyBy(function ($item) {
                    return $item->field.'#'.$item->localeId;
                });
                foreach ($fieldsToSave as $fieldName => $value) {
                    if (is_array($value)) {
                        foreach ($value as $localeName => $v) {
                            $locale = Arr::get($locales, $localeName);
                            if (!$locale) {
                                throw new ServerException('Locale with name '.$localeName.' not found');
                            }
                            $translation = Arr::get($translations, $fieldName.'#'.$locale->id);
                            $this->createTranslation($translation, $locale, $fieldName, $v);
                        }
                    } else {
                        $locale = LocaleService::current();
                        $translation = Arr::get($translations, $fieldName.'#'.$locale->id);
                        $this->createTranslation($translation, $locale, $fieldName, $value);
                    }
                    unset($this->attributes[$fieldName]);
                }
            }
            $this->load('translations');
        }
    }
    
    private function createTranslation($translation, $locale, $field, $value)
    {
        if ($translation) {
            if ($value === null || $value === '') {
                $translation->delete();
            } else {
                $translation->value = $value;
                $translation->save();
            }
        } else if (!is_null($value) && $value !== '') {
            Translation::create([
                'value' => $value,
                'localeId' => $locale->id,
                'owner' => $this,
                'field' => $field,
            ]);
        }
    }
    
    private function createCustomTranslation(&$translations, $locale, $field, $value)
    {
        $translation = Arr::get($translations, $locale->id);
        
        if ($value === null && $translation === null) {
            return;
        }
        
        if ($translation === null) {
            $translation = new TranslationCustom();
            $translation->id = $this->id;
            $translation->localeId = $locale->id;
            $translations[$locale->id] = $translation;
        }
        $translations[$locale->id]->{$field} = $value;
    }
}
