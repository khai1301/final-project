{{-- Footer --}}
<footer class="py-5 text-white" style="background-color: var(--dark); border-top: 1px solid var(--gray-800);">
    <div class="container">
        {{-- Footer Content --}}
        <div class="row g-4 mb-5">
            {{-- Brand Column --}}
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" 
                         style="width: 2rem; height: 2rem; background: var(--primary);">
                        <span class="material-symbols-outlined" style="font-size: 1.125rem;">school</span>
                    </div>
                    <h5 class="mb-0 fw-bold">SmartTutor</h5>
                </div>
                <p class="small mb-3" style="color: var(--gray-600); line-height: 1.75;">
                    {{ __('ui.footer_tagline') }}
                </p>
                <div class="mt-4">
                    <p class="mb-2 small description-text text-white-50">
                        <i class="bi bi-geo-alt-fill me-2 text-primary"></i> Hà Nội, Việt Nam
                    </p>
                    <p class="mb-2 small description-text text-white-50">
                        <i class="bi bi-envelope-fill me-2 text-primary"></i> support@smarttutor.vn
                    </p>
                    <p class="mb-0 small description-text text-white-50">
                        <i class="bi bi-telephone-fill me-2 text-primary"></i> 0123 456 789
                    </p>
                </div>
            </div>

            {{-- Learn Column --}}
            <div class="col-md-3">
                <h6 class="fw-bold mb-3">{{ __('ui.learn_section') }}</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('tutors.browse') }}" class="text-decoration-none small" style="color: var(--gray-600);">{{ __('ui.find_tutors') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none small" style="color: var(--gray-600);">{{ __('ui.online_courses') }}</a></li>
                    <li class="mb-2"><a href="{{ route('tutors.browse') }}" class="text-decoration-none small" style="color: var(--gray-600);">{{ __('ui.by_subject') }}</a></li>
                </ul>
            </div>

            {{-- Teach Column --}}
            <div class="col-md-3">
                <h6 class="fw-bold mb-3">{{ __('ui.teach_section') }}</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('register') }}" class="text-decoration-none small" style="color: var(--gray-600);">{{ __('ui.become_tutor') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none small" style="color: var(--gray-600);">{{ __('ui.tutor_rules') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none small" style="color: var(--gray-600);">{{ __('ui.success_stories') }}</a></li>
                </ul>
            </div>

            {{-- Support Column --}}
            <div class="col-md-3">
                <h6 class="fw-bold mb-3">{{ __('ui.support_section') }}</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-decoration-none small" style="color: var(--gray-600);">{{ __('ui.help_center') }}</a></li>
                    <li class="mb-2"><a href="mailto:support@smarttutor.vn" class="text-decoration-none small" style="color: var(--gray-600);">{{ __('ui.contact') }}</a></li>
                </ul>
            </div>
        </div>

        {{-- Footer Bottom --}}
        <div class="pt-4" style="border-top: 1px solid var(--gray-800);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <p class="mb-0 small" style="color: #6b7280;">{{ __('ui.copyright') }}</p>
                <div class="d-flex gap-4 small" style="color: #6b7280;">
                    <a href="#" class="text-decoration-none" style="color: #6b7280;">{{ __('ui.privacy') }}</a>
                    <a href="#" class="text-decoration-none" style="color: #6b7280;">{{ __('ui.terms') }}</a>
                    <a href="#" class="text-decoration-none" style="color: #6b7280;">{{ __('ui.sitemap') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
