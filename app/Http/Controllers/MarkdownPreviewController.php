<?php

namespace App\Http\Controllers;

use App\Support\Ui;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Vorschau für das Markdown-Eingabefeld (<x-ui.markdown-editor>): rendert den Text mit demselben
 * sicheren Renderer wie die spätere Anzeige (HTML entfernt, unsichere Links aus).
 */
class MarkdownPreviewController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => ['nullable', 'string', 'max:50000'],
        ]);

        return response()->json([
            'html' => Ui::markdown($validated['text'] ?? ''),
        ]);
    }
}
