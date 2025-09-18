<div class="skeleton-main-wrap skeleton-main-wrap-main skeleton-main-wrap-women">
    <div class="skeleton-info-wrap">
        <div class="skeleton-image-wrap">
            <img src="{{ url('assets/images/women-skeleton.svg') }}" class="skeleton-img" alt="women-skeleton">

            <button type="button" class="red-dot women left-bicept" data-target="left-bicept">
                <div class="pulse-effect">
                    <div class="pulse-dot"></div>
                </div>
            </button>

            <button type="button" class="red-dot women right-bicept" data-target="right-bicept">
                <div class="pulse-effect">
                    <div class="pulse-dot"></div>
                </div>
            </button>

            <button type="button" class="red-dot women shoulders" data-target="shoulders">
                <div class="pulse-effect">
                    <div class="pulse-dot"></div>
                </div>
            </button>

            <button type="button" class="red-dot women chest" data-target="chest">
                <div class="pulse-effect">
                    <div class="pulse-dot"></div>
                </div>
            </button>

            <button type="button" class="red-dot women waist" data-target="waist">
                <div class="pulse-effect">
                    <div class="pulse-dot"></div>
                </div>
            </button>

            <button type="button" class="red-dot women left-thigh" data-target="left-thigh">
                <div class="pulse-effect">
                    <div class="pulse-dot"></div>
                </div>
            </button>

            <button type="button" class="red-dot women right-thigh" data-target="right-thigh">
                <div class="pulse-effect">
                    <div class="pulse-dot"></div>
                </div>
            </button>

            <button type="button" class="red-dot women left-calf" data-target="left-calf">
                <div class="pulse-effect">
                    <div class="pulse-dot"></div>
                </div>
            </button>

            <button type="button" class="red-dot women right-calf" data-target="right-calf">
                <div class="pulse-effect">
                    <div class="pulse-dot"></div>
                </div>
            </button>

            <div class="skeleton-info">
                <ul>
                    @if(!empty($data) && !empty($data['female_left_bicep']))
                    <li id="left-bicept">
                        <div class="skeleton-body-part-info">
                            <div class="title">Left Bicep</div>
                            <div class="description">
                                <p>{{$data['female_left_bicep']}}</p>
                            </div>
                            <div class="close-btn">
                                <a href="javascript:;">X</a>
                            </div>
                        </div>
                    </li>
                    @endif
                    @if(!empty($data) && !empty($data['female_right_bicep']))
                    <li id="right-bicept">
                        <div class="skeleton-body-part-info">
                            <div class="title">Right Bicept</div>
                            <div class="description">
                                <p>{{$data['female_right_bicep']}}</p>
                            </div>
                            <div class="close-btn">
                                <a href="javascript:;">X</a>
                            </div>
                        </div>
                    </li>
                    @endif
                    @if(!empty($data) && !empty($data['female_shoulders']))
                    <li id="shoulders">
                        <div class="skeleton-body-part-info">
                            <div class="title">Shoulders</div>
                            <div class="description">
                                <p>{{$data['female_shoulders']}}</p>
                            </div>
                            <div class="close-btn">
                                <a href="javascript:;">X</a>
                            </div>
                        </div>
                    </li>
                    @endif
                    @if(!empty($data) && !empty($data['female_chest']))
                    <li id="chest">
                        <div class="skeleton-body-part-info">
                            <div class="title">Chest</div>
                            <div class="description">
                                <p>{{$data['female_chest']}}</p>
                            </div>
                            <div class="close-btn">
                                <a href="javascript:;">X</a>
                            </div>
                        </div>
                    </li>
                    @endif
                    @if(!empty($data) && !empty($data['female_waist']))
                    <li id="waist">
                        <div class="skeleton-body-part-info">
                            <div class="title">Waist</div>
                            <div class="description">
                                <p>{{$data['female_waist']}}</p>
                            </div>
                            <div class="close-btn">
                                <a href="javascript:;">X</a>
                            </div>
                        </div>
                    </li>
                    @endif
                    @if(!empty($data) && !empty($data['female_left_thigh']))
                    <li id="left-thigh">
                        <div class="skeleton-body-part-info">
                            <div class="title">Left Thigh</div>
                            <div class="description">
                                <p>{{$data['female_left_thigh']}}</p>
                            </div>
                            <div class="close-btn">
                                <a href="javascript:;">X</a>
                            </div>
                        </div>
                    </li>
                    @endif
                    @if(!empty($data) && !empty($data['female_right_thigh']))
                    <li id="right-thigh">
                        <div class="skeleton-body-part-info">
                            <div class="title">Right Thigh</div>
                            <div class="description">
                                <p>{{$data['female_right_thigh']}}</p>
                            </div>
                            <div class="close-btn">
                                <a href="javascript:;">X</a>
                            </div>
                        </div>
                    </li>
                    @endif
                    @if(!empty($data) && !empty($data['female_left_calf']))
                    <li id="left-calf">
                        <div class="skeleton-body-part-info">
                            <div class="title">Left Calf</div>
                            <div class="description">
                                <p>{{$data['female_left_calf']}}</p>
                            </div>
                            <div class="close-btn">
                                <a href="javascript:;">X</a>
                            </div>
                        </div>
                    </li>
                    @endif
                    @if(!empty($data) && !empty($data['female_right_calf']))
                    <li id="right-calf">
                        <div class="skeleton-body-part-info">
                            <div class="title">Right Calf</div>
                            <div class="description">
                                <p>{{$data['female_right_calf']}}</p>
                            </div>
                            <div class="close-btn">
                                <a href="javascript:;">X</a>
                            </div>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>