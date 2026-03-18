<div class="flex flex-col gap-8">
    <div>
        <flux:heading size="xl" level="1">{{ __('Email Settings') }}</flux:heading>
        <flux:subheading size="lg">{{ __('Configure SMTP settings for outgoing emails') }}</flux:subheading>
    </div>

    <flux:separator variant="subtle" />

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    @if ($testStatus === 'success')
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ __('Test email sent successfully! Check your inbox.') }}</flux:callout.heading>
        </flux:callout>
    @elseif (str_starts_with($testStatus, 'error:'))
        <flux:callout variant="danger" icon="x-circle">
            <flux:callout.heading>{{ __('Test email failed:') }}</flux:callout.heading>
            <flux:callout.text>{{ Str::after($testStatus, 'error:') }}</flux:callout.text>
        </flux:callout>
    @endif

    <div class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field class="sm:col-span-2">
                    <flux:label>{{ __('SMTP Host') }} *</flux:label>
                    <flux:input wire:model="host" placeholder="smtp.hostinger.com" />
                    <flux:error name="host" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Port') }} *</flux:label>
                    <flux:input wire:model="port" type="number" placeholder="465" />
                    <flux:error name="port" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Encryption') }} *</flux:label>
                    <x-custom-select wire:model="encryption" placeholder="SSL" :options="['ssl' => 'SSL', 'tls' => 'TLS', 'starttls' => 'STARTTLS', '' => 'None']" />
                    <flux:error name="encryption" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Username') }} *</flux:label>
                    <flux:input wire:model="username" placeholder="noreply@yourdomain.com" />
                    <flux:error name="username" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Password') }} *</flux:label>
                    <flux:input wire:model="password" type="password"
                        placeholder="{{ __('Leave blank to keep current password') }}" />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('From Address') }} *</flux:label>
                    <flux:input wire:model="from_address" type="email" placeholder="noreply@yourdomain.com" />
                    <flux:error name="from_address" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('From Name') }} *</flux:label>
                    <flux:input wire:model="from_name" placeholder="PT. Roda Jaya Sakti" />
                    <flux:error name="from_name" />
                </flux:field>
            </div>

            <div class="flex items-center gap-3">
                <flux:button type="submit" variant="primary" icon="check">
                    {{ __('Save Settings') }}
                </flux:button>

                <flux:button type="button" wire:click="sendTestEmail" variant="ghost" icon="paper-airplane"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="sendTestEmail">{{ __('Send Test Email') }}</span>
                    <span wire:loading wire:target="sendTestEmail">{{ __('Sending...') }}</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>