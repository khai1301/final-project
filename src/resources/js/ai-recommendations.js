/**
 * AI Recommendation System - Frontend Integration
 * Multi-layer caching: localStorage (1h) + Laravel cache (24h)
 * Auto-loads recommendations without user action
 */

class AIRecommendations {
    constructor() {
        this.CACHE_DURATION = 3600000; // 1 hour in milliseconds  
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.setupEventListeners();
                this.autoLoadRecommendations();
            });
        } else {
            this.setupEventListeners();
            this.autoLoadRecommendations();
        }
    }

    setupEventListeners() {
        // Manual refresh button (optional)
        document.querySelectorAll('[data-ai-refresh]').forEach(button => {
            button.addEventListener('click', (e) => this.handleRefreshClick(e));
        });
    }

    /**
     * Auto-load recommendations on page load
     */
    autoLoadRecommendations() {
        // For students: Auto-load tutor recommendations if requestId exists
        const tutorContainer = document.querySelector('[data-ai-tutors]');
        if (tutorContainer) {
            const requestId = tutorContainer.dataset.requestId;
            if (requestId) {
                this.loadTutorRecommendations(requestId, tutorContainer);
            }
        }

        // For tutors: Auto-load request recommendations if tutorProfileId exists
        const requestContainer = document.querySelector('[data-ai-requests]');
        if (requestContainer) {
            const tutorProfileId = requestContainer.dataset.tutorProfileId;
            if (tutorProfileId) {
                this.loadRequestRecommendations(tutorProfileId, requestContainer);
            }
        }
    }

    /**
     * Load tutor recommendations with caching
     */
    async loadTutorRecommendations(requestId, container) {
        // Check localStorage first
        const cached = this.getCachedData('tutors', requestId);
        if (cached) {
            console.log('📦 Loading tutor recommendations from cache');
            this.displayInline(cached.data, 'tutors', container);
            this.showCacheIndicator(container, cached.cached_at);
            return;
        }

        // Load from server (which checks Laravel cache)
        try {
            this.showLoading(container);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.content;
            }

            const response = await fetch(`/api/recommendations/tutors/${requestId}`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.data.length > 0) {
                // Cache in localStorage
                this.setCachedData('tutors', requestId, data);

                this.displayInline(data.data, 'tutors', container);
                console.log('🤖 Loaded fresh tutor recommendations from AI');
            } else {
                this.showEmptyState(container, data.message || 'Không tìm thấy gia sư phù hợp');
            }
        } catch (error) {
            console.error('Error loading tutor recommendations:', error);
            this.showError(container, error.message);
        } finally {
            this.hideLoading(container);
        }
    }

    /**
     * Load request recommendations with caching
     */
    async loadRequestRecommendations(tutorProfileId, container) {
        // Check localStorage first
        const cached = this.getCachedData('requests', tutorProfileId);
        if (cached) {
            console.log('📦 Loading request recommendations from cache');
            this.displayInline(cached.data, 'requests', container);
            this.showCacheIndicator(container, cached.cached_at);
            return;
        }

        // Load from server
        try {
            this.showLoading(container);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.content;
            }

            const response = await fetch(`/api/recommendations/requests/${tutorProfileId}`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.setCachedData('requests', tutorProfileId, data);

                this.displayInline(data.data, 'requests', container);
                console.log('🤖 Loaded fresh request recommendations from AI');
            } else {
                this.showEmptyState(container, data.message || 'Không tìm thấy yêu cầu phù hợp');
            }
        } catch (error) {
            console.error('Error loading request recommendations:', error);
            this.showError(container, error.message);
        } finally {
            this.hideLoading(container);
        }
    }

    /**
     * Get cached data from localStorage
     */
    getCachedData(type, id) {
        const cacheKey = `ai_rec_${type}_${id}`;
        const cached = localStorage.getItem(cacheKey);

        if (!cached) return null;

        try {
            const data = JSON.parse(cached);
            const now = Date.now();

            // Check expiration
            if (now - data.cached_at > this.CACHE_DURATION) {
                localStorage.removeItem(cacheKey);
                return null;
            }

            return data;
        } catch (e) {
            localStorage.removeItem(cacheKey);
            return null;
        }
    }

    /**
     * Save to localStorage cache
     */
    setCachedData(type, id, data) {
        const cacheKey = `ai_rec_${type}_${id}`;
        const cacheData = {
            ...data,
            cached_at: Date.now()
        };

        try {
            localStorage.setItem(cacheKey, JSON.stringify(cacheData));
        } catch (e) {
            console.warn('localStorage full:', e);
        }
    }

    /**
     * Display recommendations inline (not modal)
     */
    displayInline(items, type, container) {
        if (type === 'tutors') {
            container.innerHTML = this.createTutorCards(items);
        } else {
            container.innerHTML = this.createRequestCards(items);
        }
    }

    /**
     * Create tutor cards HTML - matching existing design 100%
     */
    createTutorCards(tutors) {
        // Check if user is authenticated student
        const isStudent = document.querySelector('meta[name="user-role"]')?.content === 'student';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        return tutors.slice(0, 4).map((tutor, index) => {
            // Avatar URL with S3 fallback (like existing cards)
            const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(tutor.tutor_name)}&size=100`;

            // Action button HTML
            let actionButton;
            if (isStudent && csrfToken) {
                // Student sees "Connect" button
                actionButton = `
                    <form action="/matching/connect" method="POST" class="w-100" style="margin: 0;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="tutor_id" value="${tutor.user_id}">
                        <button type="submit" class="home-tutor-view-btn" style="width: auto;">
                            Kết nối ngay
                        </button>
                    </form>
                `;
            } else {
                // Others see "View profile"
                actionButton = `<a href="/tutors/${tutor.user_id}" class="home-tutor-view-btn">Xem profile</a>`;
            }

            return `
                <div class="col-sm-6 col-lg-3">
                    <div class="home-tutor-card">
                        <div class="home-tutor-header">
                            <div class="home-tutor-avatar-wrapper">
                                <img src="${avatarUrl}" 
                                     alt="${this.escapeHtml(tutor.tutor_name)}" 
                                     class="home-tutor-avatar">
                                <div class="home-tutor-verified">
                                    <span class="material-symbols-outlined">check</span>
                                </div>
                            </div>
                            <div class="home-tutor-rating">
                                <span class="material-symbols-outlined">star</span>
                                ${tutor.rating_avg || '5.0'}
                            </div>
                        </div>
                        <div>
                            <h3 class="home-tutor-name">${this.escapeHtml(tutor.tutor_name)}</h3>
                            <p class="home-tutor-subject">${this.escapeHtml(tutor.education || 'Gia sư')}</p>
                        </div>
                        <div class="ai-match-info mb-3">
                            <div class="ai-match-badge">
                                <span class="material-symbols-outlined">psychology</span>
                                <strong>${tutor.match_score}% Match</strong>
                            </div>
                            <p class="ai-match-reason small text-muted mb-0">
                                ${this.escapeHtml(tutor.match_reason)}
                            </p>
                        </div>
                        <div class="home-tutor-footer">
                            <span class="home-tutor-price">
                                ${Math.round(tutor.hourly_rate_min / 1000)}k-${Math.round(tutor.hourly_rate_max / 1000)}k
                                <span class="home-tutor-price-unit">₫/giờ</span>
                            </span>
                            ${actionButton}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Create request cards HTML
     */
    createRequestCards(requests) {
        return requests.slice(0, 3).map((req, index) => `
            <div class="col-md-6 col-lg-4">
                <div class="home-request-card position-relative">
                    <div class="match-badge">
                        <span class="material-symbols-outlined">psychology</span>
                        ${req.match_score}%
                    </div>
                    <div class="home-request-header">
                        <span class="home-request-badge">${this.escapeHtml(req.subject)}</span>
                        <span class="home-request-time">
                            <span class="material-symbols-outlined">schedule</span>
                            Mới nhất
                        </span>
                    </div>
                    <div>
                        <h3 class="home-request-title">${this.escapeHtml(req.title)}</h3>
                        <p class="home-request-description">${this.escapeHtml(req.description || '').substring(0, 100)}...</p>
                    </div>
                    <div class="match-reason small mb-2">
                        <strong>AI:</strong> ${this.escapeHtml(req.match_reason)}
                    </div>
                    <div class="home-request-footer">
                        <div class="home-request-info">
                            <span class="material-symbols-outlined">payments</span>
                            ${Math.round(req.budget_min / 1000)}k-${Math.round(req.budget_max / 1000)}k₫/giờ
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    showLoading(container) {
        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Đang phân tích với AI...</p></div>';
    }

    hideLoading(container) {
        // Loading is replaced by content
    }

    showEmptyState(container, message) {
        container.innerHTML = `<div class="alert alert-info">${message}</div>`;
    }

    showError(container, errorMessage = '') {
        const message = errorMessage ? `: ${errorMessage}` : '';
        container.innerHTML = `<div class="alert alert-danger">Có lỗi xảy ra khi tải gợi ý${message}</div>`;
    }

    showCacheIndicator(container, cachedAt) {
        const minutes = Math.floor((Date.now() - cachedAt) / 60000);
        const timeAgo = minutes < 1 ? 'vừa xong' : `${minutes} phút trước`;

        const indicator = document.createElement('small');
        indicator.className = 'text-muted cache-indicator';
        indicator.textContent = `(Đã lưu ${timeAgo})`;
        container.insertBefore(indicator, container.firstChild);
    }

    handleRefreshClick(event) {
        event.preventDefault();
        const button = event.currentTarget;
        const type = button.dataset.aiRefresh;
        const id = button.dataset.id;

        // Clear cache
        localStorage.removeItem(`ai_rec_${type}_${id}`);

        // Reload
        if (type === 'tutors') {
            const container = document.querySelector('[data-ai-tutors]');
            this.loadTutorRecommendations(id, container);
        } else {
            const container = document.querySelector('[data-ai-requests]');
            this.loadRequestRecommendations(id, container);
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize
const aiRecommendations = new AIRecommendations();
