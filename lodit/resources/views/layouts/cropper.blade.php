<!-- Cropper.js Library Integration -->
<!-- Add this to your head or before closing body tag -->

<!-- Cropper CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

<!-- Cropper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<style>
    /* Custom Cropper Styling */
    .cropper-modal {
        background-color: rgba(0, 0, 0, 0.9) !important;
    }

    .cropper-bg {
        background-image: linear-gradient(45deg, #333 25%, transparent 25%, transparent 75%, #333 75%, #333),
                         linear-gradient(45deg, #333 25%, transparent 25%, transparent 75%, #333 75%, #333) !important;
        background-size: 20px 20px !important;
        background-position: 0 0, 10px 10px !important;
        background-color: #1a1a1a !important;
    }

    .cropper-face {
        background-color: rgba(30, 58, 138, 0.7) !important;
    }

    .cropper-line {
        background-color: #1e3a8a !important;
    }

    .cropper-point {
        background-color: #1e3a8a !important;
    }

    .cropper-grid {
        background-image: 
            linear-gradient(0deg, transparent 24%, rgba(30, 58, 138, 0.5) 25%, rgba(30, 58, 138, 0.5) 26%, transparent 27%, transparent 74%, rgba(30, 58, 138, 0.5) 75%, rgba(30, 58, 138, 0.5) 76%, transparent 77%, transparent),
            linear-gradient(90deg, transparent 24%, rgba(30, 58, 138, 0.5) 25%, rgba(30, 58, 138, 0.5) 26%, transparent 27%, transparent 74%, rgba(30, 58, 138, 0.5) 75%, rgba(30, 58, 138, 0.5) 76%, transparent 77%, transparent) !important;
        background-size: 30px 30px !important;
    }

    .cropper-dashed {
        border-color: #1e3a8a !important;
    }

    .cropper-center {
        background-color: rgba(30, 58, 138, 0.8) !important;
    }
</style>
