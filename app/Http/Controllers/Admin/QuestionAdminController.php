<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = Question::with('category')->orderByDesc('id');

        if ($request->filled('category_id')) {
            $q->where('category_id', $request->category_id);
        }

        $questions = $q->paginate(20);
        $categories = Category::orderBy('name')->get();

        return view('admin.questions.index', compact('questions','categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.questions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required','exists:categories,id'],
            'prompt' => ['required','string'],
            'a' => ['required','string','max:255'],
            'b' => ['required','string','max:255'],
            'c' => ['nullable','string','max:255'],
            'd' => ['nullable','string','max:255'],
            'correct' => ['required','in:a,b,c,d'],
            'explanation' => ['nullable','string'],
            'difficulty' => ['required','integer','min:1','max:5'],
        ]);

        Question::create($data);

        return redirect()->route('admin.questions.index')->with('status', 'Întrebarea a fost creată.');
    }

    public function edit(Question $question)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.questions.edit', compact('question','categories'));
    }

    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'category_id' => ['required','exists:categories,id'],
            'prompt' => ['required','string'],
            'a' => ['required','string','max:255'],
            'b' => ['required','string','max:255'],
            'c' => ['nullable','string','max:255'],
            'd' => ['nullable','string','max:255'],
            'correct' => ['required','in:a,b,c,d'],
            'explanation' => ['nullable','string'],
            'difficulty' => ['required','integer','min:1','max:5'],
        ]);

        $question->update($data);

        return redirect()->route('admin.questions.index')->with('status', 'Întrebarea a fost actualizată.');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('admin.questions.index')->with('status', 'Întrebarea a fost ștearsă.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('csv');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return back()->withErrors(['csv' => 'Nu pot citi fișierul.']);
        }

        // Detectăm delimiter (virgulă / punct și virgulă)
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return back()->withErrors(['csv' => 'Fișierul este gol.']);
        }
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        // Rewind și citim header-ul cu fgetcsv
        rewind($handle);
        $rawHeader = fgetcsv($handle, 0, $delimiter);
        if (!$rawHeader) {
            fclose($handle);
            return back()->withErrors(['csv' => 'Header CSV lipsă/invalid.']);
        }

        // Curățăm BOM + whitespace
        $header = array_map(function ($h) {
            $h = is_string($h) ? trim($h) : '';
            $h = preg_replace('/^\xEF\xBB\xBF/', '', $h); // UTF-8 BOM
            return strtolower($h);
        }, $rawHeader);

        // Coloane acceptate (flexibil)
        // Minim: category_slug sau category, prompt, a, b, correct
        $requiredAnyCategory = ['category_slug', 'category', 'categorie', 'category_name', 'name'];
        $required = ['prompt', 'a', 'b', 'correct'];

        $hasCategory = count(array_intersect($requiredAnyCategory, $header)) > 0;
        foreach ($required as $col) {
            if (!in_array($col, $header, true)) {
                fclose($handle);
                return back()->withErrors([
                    'csv' => "Lipsește coloana obligatorie: {$col}. Coloanele detectate: " . implode(', ', $header),
                ]);
            }
        }
        if (!$hasCategory) {
            fclose($handle);
            return back()->withErrors([
                'csv' => 'Lipsește coloana de categorie. Accept: category_slug / category / categorie / category_name.',
            ]);
        }

        // Mapăm index-urile
        $idx = array_flip($header);

        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                // rând gol
                if (count(array_filter($row, fn($v) => trim((string)$v) !== '')) === 0) {
                    continue;
                }

                $get = function (string $key, array $aliases = []) use ($row, $idx) {
                    foreach (array_merge([$key], $aliases) as $k) {
                        $k = strtolower($k);
                        if (isset($idx[$k])) {
                            return trim((string)($row[$idx[$k]] ?? ''));
                        }
                    }
                    return '';
                };

                $catRaw = $get('category_slug', ['category', 'categorie', 'category_name', 'name']);
                $prompt = $get('prompt');
                $a      = $get('a');
                $b      = $get('b');
                $c      = $get('c');
                $d      = $get('d');
                $correct= strtolower($get('correct'));
                $difficulty = $get('difficulty', ['dificultate']);
                $explanation= $get('explanation', ['explicatie', 'exlicație', 'explicație']);

                if ($catRaw === '' || $prompt === '' || $a === '' || $b === '' || !in_array($correct, ['a','b','c','d'], true)) {
                    $skipped++;
                    continue;
                }

                $slug = Str::slug($catRaw);
                $category = Category::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => Str::of($catRaw)->trim()->headline()]
                );

                Question::create([
                    'category_id' => $category->id,
                    'prompt'      => $prompt,
                    'a'           => $a,
                    'b'           => $b,
                    'c'           => $c !== '' ? $c : null,
                    'd'           => $d !== '' ? $d : null,
                    'correct'     => $correct,
                    'difficulty'  => max(1, min(5, (int)($difficulty ?: 2))),
                    'explanation' => $explanation !== '' ? $explanation : null,
                ]);

                $imported++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }

        fclose($handle);

        return redirect()
            ->route('admin.questions.index')
            ->with('status', "Import finalizat: {$imported} adăugate, {$skipped} ignorate. Delimiter detectat: {$delimiter}");
    }


}
