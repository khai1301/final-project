<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private $openaiApiKey;
    private $openaiEndpoint = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.api_key');
        
        if (empty($this->openaiApiKey)) {
            throw new \Exception('OPENAI_API_KEY not configured in .env');
        }
    }

    /**
     * Parse CV file and extract tutor profile information
     */
    public function parseCVFile(string $filePath): array
    {
        try {
            // Step 1: Try to extract text from file
            $text = null;
            $useVision = false;
            
            try {
                $text = $this->extractText($filePath);
            } catch (\Exception $e) {
                // If text extraction fails, try vision API for PDFs
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if ($extension === 'pdf') {
                    Log::info('Text extraction failed, falling back to Gemini Vision', [
                        'file' => $filePath,
                        'error' => $e->getMessage()
                    ]);
                    $useVision = true;
                } else {
                    throw $e;
                }
            }

            // Step 2: Parse with AI
            if ($useVision) {
                $parsedData = $this->parseWithVision($filePath);
            } else {
                $parsedData = $this->callOpenAI($text);
            }

            return $parsedData;

        } catch (\Exception $e) {
            Log::error('AI CV parsing failed', [
                'error' => $e->getMessage(),
                'file' => $filePath
            ]);
            throw $e;
        }
    }

    /**
     * Extract text from PDF or DOCX file
     */
    private function extractText(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        try {
            if ($extension === 'pdf') {
                $text = $this->extractFromPDF($filePath);
            } elseif (in_array($extension, ['doc', 'docx'])) {
                $text = $this->extractFromWord($filePath);
            } else {
                throw new \Exception('Định dạng file không được hỗ trợ: ' . $extension);
            }
            
            // Check if we got any text
            if (empty(trim($text))) {
                throw new \Exception('Không thể trích xuất text từ CV. File có thể bị hỏng hoặc là hình ảnh scan. Vui lòng thử file khác hoặc nhập thông tin thủ công.');
            }
            
            // Clean and ensure valid UTF-8
            $text = $this->sanitizeUTF8($text);
            
            // Double check after sanitization
            if (empty(trim($text))) {
                throw new \Exception('CV không chứa nội dung hợp lệ sau khi xử lý. Vui lòng thử file khác.');
            }
            
            return $text;
            
        } catch (\Exception $e) {
            Log::error('Text extraction failed', [
                'file' => $filePath,
                'extension' => $extension,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sanitize text to ensure valid UTF-8
     */
    private function sanitizeUTF8(string $text): string
    {
        // Remove invalid UTF-8 characters
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        
        // Remove null bytes and other control characters
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
        
        // Normalize whitespace
        $text = preg_replace('/\s+/u', ' ', $text);
        
        return trim($text);
    }

    /**
     * Extract text from PDF using Smalot PdfParser
     */
    private function extractFromPDF(string $filePath): string
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            
            // Check if text was actually extracted
            if (empty(trim($text))) {
                throw new \Exception('PDF không chứa text có thể đọc được. Có thể đây là PDF scan (hình ảnh). Vui lòng sử dụng PDF có text hoặc nhập thông tin thủ công.');
            }
            
            // Remove non-printable characters and normalize
            $text = preg_replace('/[^\P{C}\n\r\t]/u', '', $text);
            $text = preg_replace('/\s+/', ' ', $text);
            
            return trim($text);
            
        } catch (\Exception $e) {
            // Log detailed error
            Log::error('PDF text extraction failed', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            // Throw user-friendly error
            if (str_contains($e->getMessage(), 'PDF không chứa text')) {
                throw $e;
            }
            
            throw new \Exception('Không thể đọc file PDF. Vui lòng kiểm tra file có hợp lệ không hoặc thử file khác.');
        }
    }

    /**
     * Extract text from Word document
     */
    private function extractFromWord(string $filePath): string
    {
        $phpWord = IOFactory::load($filePath);
        $text = '';
        
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . ' ';
                }
            }
        }
        
        return trim($text);
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI(string $cvText): array
    {
        $prompt = $this->buildPrompt($cvText);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->openaiApiKey,
        ])->post($this->openaiEndpoint, [
            'model' => 'gpt-4o-mini', // Fast and cheap
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a CV parsing assistant. Always respond with valid JSON only, no other text.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.2,
            'max_tokens' => 2048,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        $result = $response->json();
        $generatedText = $result['choices'][0]['message']['content'] ?? '';
        
        return $this->parseAIResponse($generatedText);
    }

    /**
     * Build prompt for Gemini
     */
    private function buildPrompt(string $cvText): string
    {
        return <<<PROMPT
Bạn là trợ lý phân tích CV. Hãy trích xuất thông tin hồ sơ gia sư từ CV sau và trả về dưới dạng JSON.

Cấu trúc JSON cần trả về:
{
    "education": "string - Trình độ học vấn",
    "experience_years": "integer - Số năm kinh nghiệm dạy học (ước tính nếu không rõ)",
    "hourly_rate_min": "integer - Mức lương tối thiểu/giờ (VNĐ, ước tính dựa trên kinh nghiệm)",
    "hourly_rate_max": "integer - Mức lương tối đa/giờ (VNĐ)",
    "bio": "string - Tóm tắt chuyên môn bằng tiếng Việt (tối đa 500 ký tự)",
    "teaching_areas": ["array of strings - Khu vực giảng dạy, ví dụ: Hà Nội, Online"],
    "subjects": ["array of strings - Các môn học, ví dụ: Toán, Vật lý, Tiếng Anh"],
    "skills": ["array of strings - Kỹ năng cụ thể, ví dụ: Giải tích, Đại số tuyến tính"]
}

Quy tắc:
1. Nếu CV không đề cập mức lương, ước tính dựa trên kinh nghiệm và trình độ
2. Trích xuất tất cả môn học được đề cập
3. Xác định khu vực giảng dạy, nếu không có thì ghi "Online"
4. Viết bio chuyên nghiệp bằng tiếng Việt
5. Trả về null cho các trường không tìm thấy trong CV
6. QUAN TRỌNG: Chỉ trả về JSON object, không thêm text nào khác

CV:
$cvText

JSON:
PROMPT;
    }

    /**
     * Parse AI response and extract JSON
     */
    private function parseAIResponse(string $response): array
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse AI response as JSON', [
                'response' => $response,
                'error' => json_last_error_msg()
            ]);
            throw new \Exception('Invalid JSON response from AI');
        }

        return $this->sanitizeData($data);
    }

    /**
     * Sanitize and validate parsed data
     */
    private function sanitizeData(array $data): array
    {
        // Helper to clean UTF-8 strings
        $cleanString = function($str) {
            if (!is_string($str)) return $str;
            $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
            $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $str);
            return trim($str);
        };
        
        return [
            'education' => isset($data['education']) ? $cleanString($data['education']) : null,
            'experience_years' => isset($data['experience_years']) ? (int)$data['experience_years'] : 0,
            'hourly_rate_min' => isset($data['hourly_rate_min']) ? (int)$data['hourly_rate_min'] : 100000,
            'hourly_rate_max' => isset($data['hourly_rate_max']) ? (int)$data['hourly_rate_max'] : 500000,
            'bio' => isset($data['bio']) ? $cleanString(substr($data['bio'], 0, 500)) : null,
            'teaching_areas' => isset($data['teaching_areas']) && is_array($data['teaching_areas']) 
                ? array_map($cleanString, $data['teaching_areas']) 
                : [],
            'subjects' => isset($data['subjects']) && is_array($data['subjects']) 
                ? array_map($cleanString, $data['subjects']) 
                : [],
            'skills' => isset($data['skills']) && is_array($data['skills']) 
                ? array_map($cleanString, $data['skills']) 
                : [],
        ];
    }

    /**
     * Parse CV using OpenAI Vision API (for scanned PDFs/images)
     */
    private function parseWithVision(string $filePath): array
    {
        try {
            // Convert PDF first page to image
            $imageData = $this->convertPDFToImage($filePath);
            
            if (!$imageData) {
                throw new \Exception('Không thể convert PDF sang hình ảnh. Vui lòng thử file khác.');
            }

            // Build vision prompt
            $prompt = $this->buildVisionPrompt();

            // Call OpenAI Vision API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
            ])->post($this->openaiEndpoint, [
                'model' => 'gpt-4o-mini', // gpt-4o supports vision
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a CV parsing assistant. Always respond with valid JSON only, no other text.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => 'data:image/jpeg;base64,' . $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'temperature' => 0.2,
                'max_tokens' => 1024,
            ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI Vision API error: ' . $response->body());
            }

            $result = $response->json();
            $generatedText = $result['choices'][0]['message']['content'] ?? '';

            return $this->parseAIResponse($generatedText);

        } catch (\Exception $e) {
            Log::error('OpenAI Vision parsing failed', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Không thể phân tích CV bằng AI Vision. Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Convert PDF first page to base64 image
     */
    private function convertPDFToImage(string $filePath): ?string
    {
        try {
            // Check if Imagick is available
            if (!extension_loaded('imagick')) {
                Log::warning('Imagick extension not available, cannot convert PDF to image');
                return null;
            }

            $imagick = new \Imagick();
            $imagick->setResolution(150, 150); // Good balance of quality and size
            $imagick->readImage($filePath . '[0]'); // Read first page only
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(85);

            // Convert to base64
            $imageBlob = $imagick->getImageBlob();
            $base64 = base64_encode($imageBlob);

            $imagick->clear();
            $imagick->destroy();

            return $base64;

        } catch (\Exception $e) {
            Log::error('PDF to image conversion failed', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Build prompt for Gemini Vision
     */
    private function buildVisionPrompt(): string
    {
        return <<<PROMPT
Bạn là trợ lý phân tích CV. Hãy đọc CV trong hình ảnh này và trích xuất thông tin hồ sơ gia sư dưới dạng JSON.

Cấu trúc JSON cần trả về:
{
    "education": "string - Trình độ học vấn",
    "experience_years": "integer - Số năm kinh nghiệm dạy học",
    "hourly_rate_min": "integer - Mức lương tối thiểu/giờ (VNĐ)",
    "hourly_rate_max": "integer - Mức lương tối đa/giờ (VNĐ)",
    "bio": "string - Tóm tắt chuyên môn (tối đa 500 ký tự)",
    "teaching_areas": ["array - Khu vực giảng dạy"],
    "subjects": ["array - Các môn học"],
    "skills": ["array - Kỹ năng cụ thể"]
}

Quy tắc:
1. Đọc kỹ toàn bộ nội dung trong CV
2. Ước tính mức lương dựa trên kinh nghiệm
3. Viết bio chuyên nghiệp bằng tiếng Việt
4. Trả về null cho trường không tìm thấy
5. QUAN TRỌNG: Chỉ trả về JSON object, không thêm text nào khác

JSON:
PROMPT;
    }
}
