<?php

namespace Tests\Feature;

use App\Models\Wish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EidWishTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_create_page_loads(): void
    {
        $this->get('/eid')
            ->assertOk()
            ->assertSeeText('اصنع تهنئة عيد الأضحى خلال ثوانٍ')
            ->assertSeeText('اصنع التهنئة الآن');
    }

    public function test_user_can_create_wish_and_is_redirected_to_public_page(): void
    {
        $response = $this->post('/eid', $this->validPayload());

        $wish = Wish::first();

        $response->assertRedirect(route('eid.show', $wish->code));
        $this->assertDatabaseHas('wishes', [
            'sender_name' => 'محمد',
            'receiver_name' => 'أحمد',
            'relationship' => 'friend',
            'style' => 'religious',
            'audio_style' => 'none',
        ]);
    }

    public function test_public_page_loads_by_code(): void
    {
        $wish = Wish::create($this->wishAttributes());

        $this->get(route('eid.show', $wish->code))
            ->assertOk()
            ->assertSeeText('عيد أضحى مبارك يا')
            ->assertSeeText($wish->receiver_name)
            ->assertSeeText('مع أطيب التهاني من '.$wish->sender_name);
    }

    public function test_invalid_relationship_fails_validation(): void
    {
        $this->post('/eid', $this->validPayload(['relationship' => 'neighbor']))
            ->assertSessionHasErrors('relationship');
    }

    public function test_invalid_style_fails_validation(): void
    {
        $this->post('/eid', $this->validPayload(['style' => 'dramatic']))
            ->assertSessionHasErrors('style');
    }

    public function test_invalid_audio_style_fails_validation(): void
    {
        $this->post('/eid', $this->validPayload(['audio_style' => 'copyrighted-song']))
            ->assertSessionHasErrors('audio_style');
    }

    public function test_views_increment_when_public_page_is_opened(): void
    {
        $wish = Wish::create($this->wishAttributes());
        $wish->forceFill(['views' => 2])->save();

        $this->get(route('eid.show', $wish->code))->assertOk();

        $this->assertSame(3, $wish->fresh()->views);
    }

    public function test_facebook_share_count_increments_using_track_route(): void
    {
        $wish = Wish::create($this->wishAttributes());
        $wish->forceFill(['facebook_shares' => 1])->save();

        $this->post(route('eid.facebook-share', $wish->code))->assertNoContent();

        $this->assertSame(2, $wish->fresh()->facebook_shares);
    }

    public function test_page_contains_facebook_share_url(): void
    {
        $wish = Wish::create($this->wishAttributes());
        $greetingUrl = route('eid.show', $wish->code);
        $shareUrl = 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($greetingUrl);

        $this->get(route('eid.show', $wish->code))
            ->assertOk()
            ->assertSee($shareUrl, false);
    }

    public function test_page_contains_create_your_own_cta(): void
    {
        $wish = Wish::create($this->wishAttributes());

        $this->get(route('eid.show', $wish->code))
            ->assertOk()
            ->assertSeeText('أعجبتك التهنئة؟ اصنع تهنئتك الآن')
            ->assertSee(route('eid.create'), false);
    }

    public function test_public_page_resolves_existing_audio_file_for_old_wish(): void
    {
        $wish = Wish::create($this->wishAttributes([
            'audio_style' => 'soft',
            'audio_path' => null,
        ]));

        $this->get(route('eid.show', $wish->code))
            ->assertOk()
            ->assertSee('/audio/eid-soft.wav', false);

        $this->assertSame('/audio/eid-soft.wav', $wish->fresh()->audio_path);
    }

    public function test_html_tags_are_not_accepted_in_names(): void
    {
        $this->post('/eid', $this->validPayload(['sender_name' => '<b>محمد</b>']))
            ->assertSessionHasErrors('sender_name');
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'sender_name' => 'محمد',
            'receiver_name' => 'أحمد',
            'relationship' => 'friend',
            'style' => 'religious',
            'audio_style' => 'none',
        ], $overrides);
    }

    private function wishAttributes(array $overrides = []): array
    {
        return array_merge([
            'sender_name' => 'محمد',
            'receiver_name' => 'أحمد',
            'relationship' => 'friend',
            'style' => 'religious',
            'audio_style' => 'none',
            'message' => 'كل عام وأنت بخير يا أحمد، تقبل الله منا ومنكم صالح الأعمال.',
            'audio_path' => null,
        ], $overrides);
    }
}
