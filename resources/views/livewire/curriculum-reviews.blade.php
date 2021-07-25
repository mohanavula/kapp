<div x-data="{ stars: @entangle('stars') }">
    <div class="row mt-2">
        <div class="col col-sm-6">
            <div class="card">
                <div class="card-header">
                  Ratings & Reviews
                </div>
                <div class="card-body">
                  <p class="card-text">{{ is_null($this->subject) ? 0 : $this->subject->reviews->count() }} Reviews</p>
                  @forelse ($this->subject ? $this->subject->reviews->reverse() : [] as $review)
                      <div class="mb-2">
                          <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                          <div class="fw-bold">{{ $review->stars }} Stars by {{ $review->author_email }}</div>
                          <div>{{ $review->review }}</div>
                      </div>
                  @empty
                      <div>No reviews yet. Be first to review.</div>
                  @endforelse
                </div>
            </div>
        </div>
        <div class="col col-sm-6">
            <div class="card">
                <div class="card-header">
                    Write a review
                </div>
                <div class="card-body">
                    <div>
                        Rating: 
                        @foreach ([1, 2, 3, 4, 5] as $star)
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" x-on:click="stars = {{ $star }}" x-bind:class="stars >= {{ $star }} ? 'text-warning' : 'text-secondary' ">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                        @endforeach
                    </div>
                    @guest
                    <div class="form-group mt-2">
                        <label for="email" class="form-label">Email</label>
                        <input class="form-control" type="email" id="email" wire:model.defer='email'>
                    </div>
                    @endguest
                    @auth
                    <div class="form-group mt-2">
                        <label for="email" class="form-label">Email</label>
                        <input class="form-control" type="email" id="email" wire:model.defer='email' disabled>
                    </div>
                    @endauth
                    <div class="form-group mt-2">
                        <label for="title" class="form-label">Give a title for your review</label>
                        <input class="form-control" type="text" id="title" wire:model.defer='title'>
                    </div>
                    <div class="form-group mt-2">
                        <label for="review" class="form-label">Your review</label>
                        <textarea class="form-control" id="review" wire:model.defer="review"></textarea>
                    </div>
                    @if ($errors->any())
                        <div class="col mt-2">
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Something's not good.</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    <div class="mt-2">
                        <button class="btn btn-success" wire:click="store">Add Review</button>
                        <button class="btn btn-danger ms-2" wire:click="resetForm">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 