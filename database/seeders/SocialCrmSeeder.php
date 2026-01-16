<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CRM\ChatConversation;
use App\Models\CRM\ChatMessage;
use App\Models\CRM\DailyLeadTrack;
use App\Models\CRM\SocialIdentity;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialCrmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Social CRM Seeder...');

        // Ensure we have a branch
        $branch = $this->ensureBranch();

        // Ensure we have an admin user
        $adminUser = $this->ensureAdminUser($branch);

        // Scenario 1: New Facebook Customer (Not linked to patient)
        $this->createScenario1_NewFacebookCustomer($branch);

        // Scenario 2: Existing Patient with Facebook (Interested lead)
        $this->createScenario2_ExistingPatient($branch);

        // Scenario 3: Closed Sale (Booked status, assigned to admin)
        $this->createScenario3_ClosedSale($branch, $adminUser);

        // Bonus: Additional scenarios for variety
        $this->createScenario4_LostLead($branch);
        $this->createScenario5_ReturningCustomer($branch, $adminUser);

        $this->command->info('Social CRM Seeder completed!');
        $this->command->info('Created 5 test conversations with various statuses.');
    }

    /**
     * Ensure a branch exists
     */
    protected function ensureBranch(): Branch
    {
        $branch = Branch::first();

        if (!$branch) {
            $this->command->info('Creating default branch...');
            $branch = Branch::create([
                'name' => 'สาขาหลัก',
                'code' => 'HQ',
                'address' => '123 ถนนสุขุมวิท กรุงเทพฯ',
                'phone' => '02-123-4567',
                'is_active' => true,
            ]);
        }

        return $branch;
    }

    /**
     * Ensure an admin user exists
     */
    protected function ensureAdminUser(Branch $branch): User
    {
        $adminUser = User::where('username', 'admin')->first();

        if (!$adminUser) {
            $this->command->info('Creating admin user...');

            // Ensure admin role exists
            $adminRole = Role::firstOrCreate(
                ['name' => 'Admin'],
                ['description' => 'System Administrator']
            );

            $adminUser = User::create([
                'name' => 'System Admin',
                'username' => 'admin',
                'email' => 'admin@clinic.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'branch_id' => $branch->id,
                'is_active' => true,
            ]);
        }

        return $adminUser;
    }

    /**
     * Scenario 1: New Facebook Customer (Not linked to patient)
     * - Customer asking about prices
     * - Status: new
     */
    protected function createScenario1_NewFacebookCustomer(Branch $branch): void
    {
        $this->command->info('Creating Scenario 1: New Facebook Customer...');

        // Create Social Identity (NOT linked to patient)
        $socialIdentity = SocialIdentity::create([
            'provider' => SocialIdentity::PROVIDER_FACEBOOK,
            'provider_user_id' => 'fb_' . Str::random(15),
            'patient_id' => null, // Not linked
            'profile_name' => 'สมชาย ใจดี',
            'avatar_url' => 'https://i.pravatar.cc/150?u=somchai',
            'meta_data' => [
                'source' => 'facebook_ads',
                'ad_id' => 'ad_promo_dec_2024',
            ],
        ]);

        // Create Conversation
        $conversation = ChatConversation::create([
            'social_identity_id' => $socialIdentity->id,
            'branch_id' => $branch->id,
            'current_agent_id' => null, // Unassigned
            'status' => ChatConversation::STATUS_OPEN,
            'last_interaction_at' => now()->subMinutes(5),
        ]);

        // Create Messages
        $this->createMessage($conversation, 'customer', 'text', 'สวัสดีครับ สนใจทำกายภาพบำบัดครับ', now()->subHours(2));
        $this->createMessage($conversation, 'customer', 'text', 'รบกวนสอบถามราคาหน่อยครับ', now()->subHours(2)->addMinutes(1));
        $this->createMessage($conversation, 'customer', 'text', 'ปวดหลังเรื้อรังมาสักพักแล้วครับ', now()->subMinutes(30));
        $this->createMessage($conversation, 'customer', 'text', 'เปิดกี่โมงถึงกี่โมงครับ?', now()->subMinutes(5));

        // Create Daily Lead Track
        DailyLeadTrack::create([
            'conversation_id' => $conversation->id,
            'tracking_date' => now()->toDateString(),
            'status' => DailyLeadTrack::STATUS_NEW,
            'ad_source_id' => 'fb_ad_promo_dec',
            'utm_data' => [
                'utm_source' => 'facebook',
                'utm_medium' => 'cpc',
                'utm_campaign' => 'december_promo',
            ],
        ]);
    }

    /**
     * Scenario 2: Existing Patient with Facebook (Interested lead)
     * - Customer asking for appointment
     * - Status: interested
     */
    protected function createScenario2_ExistingPatient(Branch $branch): void
    {
        $this->command->info('Creating Scenario 2: Existing Patient (Interested)...');

        // Create Patient first
        $patient = Patient::withoutGlobalScopes()->create([
            'name' => 'วิภา รักสุขภาพ',
            'phone' => '081-234-5678',
            'email' => 'wipa@email.com',
            'gender' => 'female',
            'date_of_birth' => '1985-03-15',
            'address' => '456 ซอยสุขสวัสดิ์ กรุงเทพฯ',
            'branch_id' => $branch->id,
            'first_visit_branch_id' => $branch->id,
            'booking_channel' => 'facebook',
            'is_temporary' => false,
        ]);

        // Create Social Identity (Linked to patient)
        $socialIdentity = SocialIdentity::create([
            'provider' => SocialIdentity::PROVIDER_FACEBOOK,
            'provider_user_id' => 'fb_' . Str::random(15),
            'patient_id' => $patient->id,
            'profile_name' => 'Wipa Raksukaphap',
            'avatar_url' => 'https://i.pravatar.cc/150?u=wipa',
            'meta_data' => [
                'linked_at' => now()->subMonths(2)->toIso8601String(),
            ],
        ]);

        // Create Conversation
        $conversation = ChatConversation::create([
            'social_identity_id' => $socialIdentity->id,
            'branch_id' => $branch->id,
            'current_agent_id' => null,
            'status' => ChatConversation::STATUS_OPEN,
            'last_interaction_at' => now()->subMinutes(15),
        ]);

        // Create Messages
        $this->createMessage($conversation, 'customer', 'text', 'สวัสดีค่ะ จำได้ไหมคะ เคยมารักษาเมื่อ 2 เดือนก่อน', now()->subHours(1));
        $this->createMessage($conversation, 'user', 'text', 'สวัสดีค่ะ คุณวิภา จำได้ค่ะ ยินดีให้บริการค่ะ', now()->subMinutes(55));
        $this->createMessage($conversation, 'customer', 'text', 'อยากจะนัดมาทำกายภาพต่อค่ะ ช่วงอาทิตย์หน้าว่างไหมคะ?', now()->subMinutes(50));
        $this->createMessage($conversation, 'user', 'text', 'ค่ะ ว่างค่ะ วันจันทร์-ศุกร์ 9:00-18:00 และวันเสาร์ 9:00-15:00 ค่ะ', now()->subMinutes(45));
        $this->createMessage($conversation, 'customer', 'text', 'วันพุธ บ่ายสองโมงได้ไหมคะ?', now()->subMinutes(15));

        // Create Daily Lead Track - Interested
        DailyLeadTrack::create([
            'conversation_id' => $conversation->id,
            'tracking_date' => now()->toDateString(),
            'status' => DailyLeadTrack::STATUS_INTERESTED,
            'notes' => 'ลูกค้าเก่า กลับมาใช้บริการ สนใจนัดวันพุธ',
        ]);
    }

    /**
     * Scenario 3: Closed Sale (Booked status, assigned to admin)
     */
    protected function createScenario3_ClosedSale(Branch $branch, User $adminUser): void
    {
        $this->command->info('Creating Scenario 3: Closed Sale (Booked)...');

        // Create Patient
        $patient = Patient::withoutGlobalScopes()->create([
            'name' => 'ประยุทธ์ แข็งแรง',
            'phone' => '089-876-5432',
            'email' => 'prayuth@email.com',
            'gender' => 'male',
            'date_of_birth' => '1978-08-20',
            'address' => '789 ถนนพระราม 9 กรุงเทพฯ',
            'branch_id' => $branch->id,
            'first_visit_branch_id' => $branch->id,
            'booking_channel' => 'facebook',
            'is_temporary' => false,
        ]);

        // Create Social Identity
        $socialIdentity = SocialIdentity::create([
            'provider' => SocialIdentity::PROVIDER_FACEBOOK,
            'provider_user_id' => 'fb_' . Str::random(15),
            'patient_id' => $patient->id,
            'profile_name' => 'Prayuth Kaengrang',
            'avatar_url' => 'https://i.pravatar.cc/150?u=prayuth',
        ]);

        // Create Conversation - Assigned to admin
        $conversation = ChatConversation::create([
            'social_identity_id' => $socialIdentity->id,
            'branch_id' => $branch->id,
            'current_agent_id' => $adminUser->id, // Assigned!
            'status' => ChatConversation::STATUS_OPEN,
            'last_interaction_at' => now()->subHours(3),
        ]);

        // Create Messages - Full conversation flow
        $this->createMessage($conversation, 'customer', 'text', 'สวัสดีครับ สนใจคอร์สกายภาพบำบัด 10 ครั้งครับ', now()->subDays(1));
        $this->createMessage($conversation, 'user', 'text', 'สวัสดีครับ ยินดีให้บริการครับ คอร์ส 10 ครั้ง ราคา 8,500 บาทครับ', now()->subDays(1)->addMinutes(10), $adminUser->id);
        $this->createMessage($conversation, 'customer', 'text', 'ราคานี้รวม VAT แล้วใช่ไหมครับ?', now()->subDays(1)->addMinutes(15));
        $this->createMessage($conversation, 'user', 'text', 'ใช่ครับ รวมทุกอย่างแล้วครับ', now()->subDays(1)->addMinutes(20), $adminUser->id);
        $this->createMessage($conversation, 'customer', 'text', 'โอเคครับ ขอจองคอร์สเลยครับ นัดวันศุกร์นี้ได้ไหมครับ?', now()->subHours(5));
        $this->createMessage($conversation, 'user', 'text', 'ได้ครับ จองให้เรียบร้อยแล้วครับ วันศุกร์ เวลา 10:00 น. ครับ', now()->subHours(4), $adminUser->id);
        $this->createMessage($conversation, 'customer', 'text', 'ขอบคุณครับ 🙏', now()->subHours(3));
        $this->createMessage($conversation, 'system', 'system', 'Conversation assigned to System Admin', now()->subHours(3)->subMinutes(5));

        // Create Daily Lead Track - Booked
        DailyLeadTrack::create([
            'conversation_id' => $conversation->id,
            'tracking_date' => now()->toDateString(),
            'status' => DailyLeadTrack::STATUS_BOOKED,
            'sale_closed_by' => $adminUser->id,
            'notes' => 'จองคอร์ส 10 ครั้ง 8,500 บาท นัดวันศุกร์',
        ]);
    }

    /**
     * Scenario 4: Lost Lead
     */
    protected function createScenario4_LostLead(Branch $branch): void
    {
        $this->command->info('Creating Scenario 4: Lost Lead...');

        // Create Social Identity (Guest - temporary patient created)
        $tempPatient = Patient::withoutGlobalScopes()->create([
            'name' => 'FB Lead abc123',
            'phone' => 'FB-' . Str::random(8), // Placeholder phone for temp patient
            'is_temporary' => true,
            'branch_id' => $branch->id,
            'first_visit_branch_id' => $branch->id,
            'booking_channel' => 'facebook',
            'notes' => 'Auto-created from Facebook Messenger',
        ]);

        $socialIdentity = SocialIdentity::create([
            'provider' => SocialIdentity::PROVIDER_FACEBOOK,
            'provider_user_id' => 'fb_' . Str::random(15),
            'patient_id' => $tempPatient->id,
            'profile_name' => 'Anonymous User',
            'avatar_url' => null,
        ]);

        // Create Conversation - Closed
        $conversation = ChatConversation::create([
            'social_identity_id' => $socialIdentity->id,
            'branch_id' => $branch->id,
            'current_agent_id' => null,
            'status' => ChatConversation::STATUS_CLOSED,
            'last_interaction_at' => now()->subDays(3),
        ]);

        // Create Messages
        $this->createMessage($conversation, 'customer', 'text', 'ราคาเท่าไหร่ครับ', now()->subDays(5));
        $this->createMessage($conversation, 'user', 'text', 'สวัสดีครับ กายภาพบำบัดครั้งละ 1,200 บาทครับ', now()->subDays(5)->addMinutes(30));
        $this->createMessage($conversation, 'customer', 'text', 'แพงจัง', now()->subDays(5)->addMinutes(35));
        $this->createMessage($conversation, 'system', 'system', 'Conversation closed - No response for 3 days', now()->subDays(3));

        // Create Daily Lead Track - Lost
        DailyLeadTrack::create([
            'conversation_id' => $conversation->id,
            'tracking_date' => now()->subDays(3)->toDateString(),
            'status' => DailyLeadTrack::STATUS_LOST,
            'notes' => 'ไม่ตอบกลับ 3 วัน - อาจแพงเกินไป',
        ]);
    }

    /**
     * Scenario 5: Returning Customer with multiple messages
     */
    protected function createScenario5_ReturningCustomer(Branch $branch, User $adminUser): void
    {
        $this->command->info('Creating Scenario 5: Returning Customer...');

        // Create Patient
        $patient = Patient::withoutGlobalScopes()->create([
            'name' => 'มาลี สุขใจ',
            'phone' => '062-345-6789',
            'email' => 'malee@email.com',
            'gender' => 'female',
            'date_of_birth' => '1990-12-01',
            'address' => '321 ซอยลาดพร้าว กรุงเทพฯ',
            'branch_id' => $branch->id,
            'first_visit_branch_id' => $branch->id,
            'booking_channel' => 'walk-in',
            'is_temporary' => false,
        ]);

        // Create Social Identity
        $socialIdentity = SocialIdentity::create([
            'provider' => SocialIdentity::PROVIDER_FACEBOOK,
            'provider_user_id' => 'fb_' . Str::random(15),
            'patient_id' => $patient->id,
            'profile_name' => 'Malee Sukjai',
            'avatar_url' => 'https://i.pravatar.cc/150?u=malee',
        ]);

        // Create Conversation
        $conversation = ChatConversation::create([
            'social_identity_id' => $socialIdentity->id,
            'branch_id' => $branch->id,
            'current_agent_id' => $adminUser->id,
            'status' => ChatConversation::STATUS_PENDING,
            'last_interaction_at' => now()->subMinutes(2),
        ]);

        // Create Messages - With image attachment
        $this->createMessage($conversation, 'customer', 'text', 'สวัสดีค่ะ คอร์สหมดแล้วค่ะ อยากต่อค่ะ', now()->subHours(1));
        $this->createMessage($conversation, 'user', 'text', 'สวัสดีค่ะ คุณมาลี รอสักครู่นะคะ เช็คให้ค่ะ', now()->subMinutes(55), $adminUser->id);
        $this->createMessage($conversation, 'user', 'text', 'คอร์สเดิมหมดแล้วค่ะ ต้องการต่อคอร์สเดิมหรือเปลี่ยนคอร์สใหม่คะ?', now()->subMinutes(50), $adminUser->id);
        $this->createMessage($conversation, 'customer', 'text', 'คอร์สเดิมค่ะ ราคาเท่าเดิมไหมคะ?', now()->subMinutes(30));
        $this->createMessage($conversation, 'customer', 'image', null, now()->subMinutes(25), null, 'https://via.placeholder.com/400x300?text=Receipt');
        $this->createMessage($conversation, 'customer', 'text', 'นี่ใบเสร็จครั้งก่อนค่ะ', now()->subMinutes(24));
        $this->createMessage($conversation, 'user', 'text', 'รับทราบค่ะ ราคาเท่าเดิม 8,500 บาท 10 ครั้งค่ะ', now()->subMinutes(10), $adminUser->id);
        $this->createMessage($conversation, 'customer', 'text', 'โอนเงินได้เลยไหมคะ?', now()->subMinutes(2));

        // Create Daily Lead Track - Contacted (about to close)
        DailyLeadTrack::create([
            'conversation_id' => $conversation->id,
            'tracking_date' => now()->toDateString(),
            'status' => DailyLeadTrack::STATUS_CONTACTED,
            'sale_closed_by' => $adminUser->id,
            'notes' => 'ลูกค้าต่อคอร์ส กำลังรอชำระเงิน',
        ]);
    }

    /**
     * Helper: Create a chat message
     */
    protected function createMessage(
        ChatConversation $conversation,
        string $senderType,
        string $messageType,
        ?string $content,
        $createdAt,
        ?string $senderId = null,
        ?string $mediaUrl = null
    ): ChatMessage {
        return ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message_type' => $messageType,
            'content' => $content,
            'media_url' => $mediaUrl,
            'is_read' => $senderType !== 'customer', // Customer messages unread by default
            'created_at' => $createdAt,
        ]);
    }
}
