# GitHub Copilot & AI Agent Instructions — GOXSGAME

This repository is **GOXSGAME (GOXSGROW)**, an educational gamified quiz platform built with Laravel 12 (PHP 8.4), Tailwind CSS v3, and Alpine.js.

Before modifying or adding code, refer to [AI_AGENT_DOCS.txt](file:///d:/product/GOXSGROW/AI_AGENT_DOCS.txt) and [PRD_GOXSGAME.txt](file:///d:/product/GOXSGROW/PRD_GOXSGAME.txt).

---

## 🎨 UI/UX: Neo-Brutalist Design System Contract
1. **Borders & Shadows**:
   - Containers: `border-4 border-black rounded-2xl` or `rounded-3xl` with hard offset shadows `shadow-[4px_4px_0px_#000000]` or `shadow-[6px_6px_0px_#000000]`.
   - Bouncy Buttons: `translate-x-0 translate-y-0 shadow-[4px_4px_0px_#000]` -> active `translate-y-[2px] shadow-[0px_0px_0px_#000]`.
2. **Color Palette & Absolute Text Contrast Rule**:
   - Signature Colors: Yellow `#FFE600`, Emerald `#4ADE80`, Royal Blue `#2563EB`, Cyan `#06B6D4`, Rose `#FB7185`, Purple `#A855F7`.
   - **CRITICAL**: Bright colors (`#FFE600`, `#4ADE80`, `#06B6D4`, `#FB7185`) MUST ALWAYS have pitch-black text (`#000000 !important`). NEVER use white text on them in either Light or Dark Mode.
3. **Dark Mode**:
   - Root class `html.dark`.
   - Inputs, selects, and textareas: dark background `#0F172A`, light text `#F8FAFC`, dark border `#000000`.
   - Prevent input icon overlap: on focus, `.neo-input` translates; wrap icon containers with `.relative > .pointer-events-none { z-index: 10; }`.

---

## ⚡ Backend Architecture (Laravel 12 / PHP 8.4)
1. **Route Model Binding**:
   - Resource parameter names must match controller variable names (e.g., `Route::resource('feedback', ...)` produces parameter `{feedback}` -> method parameter MUST be `DevelopmentMessage $feedback`).
2. **Environment & Config**:
   - Never call `env('GEMINI_API_KEY')` directly in controllers. Always use `config('services.gemini.api_key', env('GEMINI_API_KEY'))` to stay compatible with `php artisan config:cache`.
3. **Realtime Polling**:
   - Student game sessions poll `GET /student/room/{room}/state` every 2000ms. Do not replace this with unverified WebSockets without architectural approval.
4. **AI Resilience**:
   - Google Gemini API calls must catch exceptions and failover to fallback educational quiz templates to prevent classroom disruptions.

---

## 🧪 Testing & Code Formatting
1. Format all modified PHP code with Laravel Pint:
   ```bash
   vendor/bin/pint --format agent
   ```
2. Run test suites:
   ```bash
   php artisan test --compact
   ```
