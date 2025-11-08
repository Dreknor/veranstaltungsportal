<?php

namespace App\Services;

use App\Models\Event;

class SocialShareService
{
    /**
     * Generate Facebook share URL
     */
    public function getFacebookShareUrl(Event $event): string
    {
        $url = route('events.show', $event->slug);
        return 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url);
    }

    /**
     * Generate Twitter share URL
     */
    public function getTwitterShareUrl(Event $event): string
    {
        $url = route('events.show', $event->slug);
        $text = $event->title . ' - ' . $event->start_date->format('d.m.Y');

        return 'https://twitter.com/intent/tweet?url=' . urlencode($url) .
               '&text=' . urlencode($text) .
               '&hashtags=event,bildung';
    }

    /**
     * Generate LinkedIn share URL
     */
    public function getLinkedInShareUrl(Event $event): string
    {
        $url = route('events.show', $event->slug);
        return 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($url);
    }

    /**
     * Generate WhatsApp share URL
     */
    public function getWhatsAppShareUrl(Event $event): string
    {
        $url = route('events.show', $event->slug);
        $text = $event->title . ' - ' . $event->start_date->format('d.m.Y') . ' - ' . $url;

        return 'https://wa.me/?text=' . urlencode($text);
    }

    /**
     * Generate Email share URL
     */
    public function getEmailShareUrl(Event $event): string
    {
        $url = route('events.show', $event->slug);
        $subject = 'Event-Empfehlung: ' . $event->title;
        $body = "Ich möchte diese Veranstaltung mit dir teilen:\n\n" .
                $event->title . "\n" .
                "Datum: " . $event->start_date->format('d.m.Y H:i') . "\n" .
                "Ort: " . $event->venue_name . "\n\n" .
                "Mehr Informationen: " . $url;

        return 'mailto:?subject=' . urlencode($subject) . '&body=' . urlencode($body);
    }

    /**
     * Generate Telegram share URL
     */
    public function getTelegramShareUrl(Event $event): string
    {
        $url = route('events.show', $event->slug);
        $text = $event->title . ' - ' . $event->start_date->format('d.m.Y');

        return 'https://t.me/share/url?url=' . urlencode($url) . '&text=' . urlencode($text);
    }

    /**
     * Get all share URLs for an event
     */
    public function getAllShareUrls(Event $event): array
    {
        return [
            'facebook' => $this->getFacebookShareUrl($event),
            'twitter' => $this->getTwitterShareUrl($event),
            'linkedin' => $this->getLinkedInShareUrl($event),
            'whatsapp' => $this->getWhatsAppShareUrl($event),
            'email' => $this->getEmailShareUrl($event),
            'telegram' => $this->getTelegramShareUrl($event),
        ];
    }

    /**
     * Generate share link for copying
     */
    public function getShareableLink(Event $event): string
    {
        return route('events.show', $event->slug);
    }
}

