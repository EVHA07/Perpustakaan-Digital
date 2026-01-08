<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Membaca — {{ $book->judul }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        #pdfContainer {
            height: calc(100vh - 64px);
            width: 100vw;
            overflow-y: auto;
            scrollbar-width: none;
        }
        #pdfContainer::-webkit-scrollbar {
            display: none;
        }
        #pdfContainer canvas {
            display: block;
            margin: 0 auto;
            width: 100%;
        }
        .page-nav {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .page-nav:hover, .page-nav.visible {
            opacity: 1;
        }
        html, body {
            overflow: hidden;
        }
    </style>
</head>

<body
    class="bg-bg dark:bg-dark-bg text-text dark:text-dark-text"
    data-theme="{{ session('theme','light') }}"
    data-book-id="{{ $book->id }}"
    data-total-seconds="{{ $history->total_time_spent ?? 0 }}"
    data-last-ping-at="{{ $history->last_ping_at ? $history->last_ping_at->getTimestamp() : null }}"
    data-total-pages="{{ $book->total_pages ?? 0 }}"
    data-last-page="{{ $history->last_page ?? 1 }}"
>

<header class="fixed top-0 inset-x-0 z-40 h-16 backdrop-blur-md bg-surface/90 dark:bg-dark-surface/90 border-b border-border dark:border-dark-border">
    <div class="flex items-center justify-between h-full px-4 sm:px-6">
        <div class="flex items-center gap-3">
            <button onclick="exitReader()" class="p-2 rounded-lg hover:bg-surface-secondary dark:hover:bg-dark-border transition-colors">
                <svg class="w-5 h-5 text-text-muted dark:text-dark-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="flex flex-col">
                <span class="font-semibold truncate max-w-[200px] sm:max-w-md">{{ $book->judul }}</span>
                <span class="text-xs text-text-muted dark:text-dark-text-muted">{{ $book->kategori }}</span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <span id="readingTimer" class="font-mono font-bold text-accent dark:text-dark-accent text-sm">00:00:00</span>
            <button onclick="toggleTheme()" class="p-2 rounded-lg hover:bg-surface-secondary dark:hover:bg-dark-border transition-colors">
                <svg id="sunIcon" class="w-5 h-5 text-text-muted dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <svg id="moonIcon" class="w-5 h-5 text-text-light dark:block hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
            </button>
        </div>
    </div>
</header>

<main class="fixed inset-0 pt-16 bg-surface-secondary dark:bg-dark-bg">
    <div id="pdfContainer" class="relative"></div>

    <button onclick="prevPage()" class="page-nav absolute left-4 top-1/2 -translate-y-1/2 p-3 rounded-full backdrop-blur-md bg-surface/90 dark:bg-dark-surface/90 border border-border dark:border-dark-border shadow-lg">
        <svg class="w-6 h-6 text-text dark:text-dark-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>

    <button onclick="nextPage()" class="page-nav absolute right-4 top-1/2 -translate-y-1/2 p-3 rounded-full backdrop-blur-md bg-surface/90 dark:bg-dark-surface/90 border border-border dark:border-dark-border shadow-lg">
        <svg class="w-6 h-6 text-text dark:text-dark-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>

    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 px-6 py-3 rounded-full backdrop-blur-md bg-surface/90 dark:bg-dark-surface/90 border border-border dark:border-dark-border text-sm font-semibold shadow-lg">
        Hal <span id="currentPage">{{ $history->last_page ?? 1 }}</span> / <span id="totalPages">{{ $book->total_pages ?? '?' }}</span>
    </div>
</main>

<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

const html = document.documentElement
const savedTheme = document.body.dataset.theme
if (savedTheme === 'dark') html.classList.add('dark')

function toggleTheme() {
    html.classList.toggle('dark')
    fetch('/theme/toggle', {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            theme: html.classList.contains('dark') ? 'dark' : 'light'
        })
    })
}

let serverTotalSeconds = parseInt(document.body.dataset.totalSeconds) || 0
// PENTING: Gunakan last_ping_at dari server, BUKAN Date.now()!
// Ini mencegah reset saat page refresh
let lastPingTimestamp = parseInt(document.body.dataset.lastPingAt) || Math.floor(Date.now() / 1000)

function formatTime(sec) {
    const h = Math.floor(sec / 3600)
    const m = Math.floor((sec % 3600) / 60)
    const s = sec % 60
    return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`
}

function updateTimer() {
    // Hitung waktu yang sudah berlalu sejak server last_ping_at
    const nowTimestamp = Math.floor(Date.now() / 1000)
    const elapsedSinceLastPing = nowTimestamp - lastPingTimestamp
    const currentTotal = serverTotalSeconds + elapsedSinceLastPing
    document.getElementById('readingTimer').textContent = formatTime(currentTotal)
}

async function sendPing() {
    try {
        const response = await fetch(`/buku/${bookId}/ping`, {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
            }
        })

        const data = await response.json()

        if (data.success) {
            // Update nilai dari server (source of truth)
            serverTotalSeconds = data.total_time_spent
            // Update timestamp ke SEKARANG, bukan dari server
            lastPingTimestamp = Math.floor(Date.now() / 1000)
            updateTimer()
            console.log('✓ Ping sent! Delta:', data.delta + 's, Total:', serverTotalSeconds + 's')
        }
    } catch (error) {
        console.error('✗ Error sending ping:', error)
    }
}

// Update timer display setiap detik (visual only, bukan data persistence)
setInterval(updateTimer, 1000)
updateTimer()

// Send ping setiap 15 detik (server-side accumulation)
setInterval(sendPing, 15000)

const bookId = document.body.dataset.bookId
let currentPage = parseInt(document.body.dataset.lastPage) || 1
const totalPages = parseInt(document.body.dataset.totalPages) || 0

 const pdfUrl = "{{ asset('storage/'.$book->file_path) }}"
let pdfDoc = null
let isRendering = false
let isSaving = false

async function loadPDF() {
    if (isRendering) return
    isRendering = true

    try {
        const loadingTask = pdfjsLib.getDocument(pdfUrl)
        pdfDoc = await loadingTask.promise

        document.getElementById('totalPages').textContent = pdfDoc.numPages

        const container = document.getElementById('pdfContainer')
        container.innerHTML = ''

        for (let i = 1; i <= pdfDoc.numPages; i++) {
            const pageCanvas = document.createElement('canvas')
            const pageCtx = pageCanvas.getContext('2d')

            const page = await pdfDoc.getPage(i)
            const containerWidth = container.clientWidth

            const viewport = page.getViewport({ scale: 1 })
            const scale = containerWidth / viewport.width
            const outputScale = window.devicePixelRatio || 1

            const scaledViewport = page.getViewport({
                scale: scale * outputScale
            })

            pageCanvas.width = scaledViewport.width
            pageCanvas.height = scaledViewport.height
            pageCanvas.style.width = `${scaledViewport.width / outputScale}px`
            pageCanvas.style.height = `${scaledViewport.height / outputScale}px`
            pageCanvas.style.margin = '0 auto'
            pageCanvas.style.display = 'block'
            pageCanvas.setAttribute('data-page-num', i)

            const renderContext = {
                canvasContext: pageCtx,
                viewport: scaledViewport
            }

            await page.render(renderContext).promise
            container.appendChild(pageCanvas)
        }

        const startPageElement = container.querySelector(`[data-page-num="${currentPage}"]`)
        if (startPageElement) {
            startPageElement.scrollIntoView({ behavior: 'smooth', block: 'start' })
        }
    } catch (error) {
        console.error('Error loading PDF:', error)
        alert('Gagal memuat PDF. Silakan coba lagi.')
    } finally {
        isRendering = false
    }
}

async function renderPage(pageNum) {
    if (!pdfDoc) return

    const page = await pdfDoc.getPage(pageNum)
    const container = document.getElementById('pdfContainer')
    const containerWidth = container.clientWidth

    const viewport = page.getViewport({ scale: 1 })
    const scale = containerWidth / viewport.width
    const outputScale = window.devicePixelRatio || 1

    const scaledViewport = page.getViewport({
        scale: scale * outputScale
    })

    canvas.width = scaledViewport.width
    canvas.height = scaledViewport.height
    canvas.style.width = `${scaledViewport.width / outputScale}px`
    canvas.style.height = `${scaledViewport.height / outputScale}px`

    const renderContext = {
        canvasContext: ctx,
        viewport: scaledViewport
    }

    await page.render(renderContext).promise

    document.getElementById('currentPage').textContent = pageNum
    canvas.style.marginTop = '0'
    canvas.style.marginBottom = '0'
}

function nextPage() {
    if (pdfDoc && currentPage < pdfDoc.numPages) {
        currentPage++
        const pageElement = document.querySelector(`[data-page-num="${currentPage}"]`)
        if (pageElement) {
            pageElement.scrollIntoView({ behavior: 'smooth', block: 'start' })
        }
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--
        const pageElement = document.querySelector(`[data-page-num="${currentPage}"]`)
        if (pageElement) {
            pageElement.scrollIntoView({ behavior: 'smooth', block: 'start' })
        }
    }
}

async function saveProgress() {
    if (isSaving) return

    isSaving = true

    const container = document.getElementById('pdfContainer')
    const containerRect = container.getBoundingClientRect()
    const canvases = container.querySelectorAll('canvas')

    let maxVisibleRatio = 0
    let visiblePage = currentPage

    canvases.forEach(canvas => {
        const canvasRect = canvas.getBoundingClientRect()
        const visibleHeight = Math.min(
            canvasRect.bottom - containerRect.top,
            containerRect.bottom - canvasRect.top,
            canvasRect.height
        )
        const visibleRatio = visibleHeight / canvasRect.height

        if (visibleRatio > maxVisibleRatio) {
            maxVisibleRatio = visibleRatio
            visiblePage = parseInt(canvas.getAttribute('data-page-num'))
        }
    })

    if (visiblePage !== currentPage) {
        currentPage = visiblePage
        document.getElementById('currentPage').textContent = currentPage
    }

    try {
        const response = await fetch(`/buku/${bookId}/progress`, {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                last_page: currentPage
            })
        })

        const data = await response.json()

        if (data.success) {
            console.log('Progress saved!')
        }
    } catch (error) {
        console.error('Error saving progress:', error)
    } finally {
        isSaving = false
    }
}

async function exitReader() {
    console.log('Exiting reader...')
    await saveProgress()
    await sendPing()
    console.log('Progress saved, redirecting...')
    window.location.href = `/buku/${bookId}`
}

document.addEventListener('DOMContentLoaded', () => {
    loadPDF()

    const navButtons = document.querySelectorAll('.page-nav')
    const pdfContainer = document.getElementById('pdfContainer')

    pdfContainer.addEventListener('mouseenter', () => {
        navButtons.forEach(btn => btn.classList.add('visible'))
    })

    pdfContainer.addEventListener('mouseleave', () => {
        navButtons.forEach(btn => btn.classList.remove('visible'))
    })

    pdfContainer.addEventListener('scroll', () => {
        const containerRect = pdfContainer.getBoundingClientRect()
        const canvases = pdfContainer.querySelectorAll('canvas')

        let maxVisibleRatio = 0
        let visiblePage = currentPage

        canvases.forEach(canvas => {
            const canvasRect = canvas.getBoundingClientRect()
            const visibleHeight = Math.min(
                canvasRect.bottom - containerRect.top,
                containerRect.bottom - canvasRect.top,
                canvasRect.height
            )
            const visibleRatio = visibleHeight / canvasRect.height

            if (visibleRatio > maxVisibleRatio) {
                maxVisibleRatio = visibleRatio
                visiblePage = parseInt(canvas.getAttribute('data-page-num'))
            }
        })

        if (visiblePage !== currentPage) {
            currentPage = visiblePage
            document.getElementById('currentPage').textContent = currentPage
        }
    })

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') exitReader()
        if (e.key === 'ArrowRight') nextPage()
        if (e.key === 'ArrowLeft') prevPage()
        if (e.key === 'ArrowDown') {
            pdfContainer.scrollBy({ top: 200, behavior: 'smooth' })
        }
        if (e.key === 'ArrowUp') {
            pdfContainer.scrollBy({ top: -200, behavior: 'smooth' })
        }
    })
})

window.addEventListener('beforeunload', () => {
    saveProgress()
    sendPing()
})

window.addEventListener('resize', () => {
    const container = document.getElementById('pdfContainer')
    container.innerHTML = ''
    loadPDF()
})
</script>

</body>
</html>
