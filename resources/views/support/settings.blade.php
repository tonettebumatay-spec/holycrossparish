<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-6">

            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('support.index') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 rounded-full text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    ← Back to Support
                </a>
                <h1 class="text-2xl font-black text-gray-800 uppercase">⚙️ Support Settings</h1>
                <div class="w-20"></div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <form method="POST" action="{{ route('support.settings.update') }}" class="space-y-6">
                    @csrf

                    <!-- Auto-reply -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                            Auto-Reply Message
                        </label>
                        <textarea name="auto_reply_message" rows="4" required
                                  class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">{{ $settings['auto_reply_message'] ?? '' }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">
                            Ito ang ipapadala sa user kapag offline ang admin.
                        </p>
                    </div>

                    <!-- Auto-reply Enabled -->
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="auto_reply_enabled" id="auto_reply_enabled" value="1"
                               {{ ($settings['auto_reply_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                               class="w-5 h-5 text-purple-600 rounded">
                        <label for="auto_reply_enabled" class="text-sm font-semibold text-gray-700">
                            Enable auto-reply
                        </label>
                    </div>

                    <!-- Office Hours -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                            Office Hours
                        </label>
                        <input type="text" name="office_hours" value="{{ $settings['office_hours'] ?? '' }}"
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                            Contact Email
                        </label>
                        <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}"
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">
                    </div>

                    <!-- Contact Address -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                            Contact Address
                        </label>
                        <textarea name="contact_address" rows="2"
                                  class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-purple-500">{{ $settings['contact_address'] ?? '' }}</textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-black uppercase tracking-widest">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>