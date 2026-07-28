// Driver number-flow (https://github.com/barvian/number-flow).
//
// Registra o web component <number-flow> e inicializa todo elemento
// number-flow[data-value] renderizado pelo pacote:
//
// 1. Primeiro render: exibe 0 e, após data-delay (ms), anima até data-value.
// 2. Atualizações (Livewire poll/morph): quando o morph troca o atributo
//    data-value, o MutationObserver anima do valor atual para o novo.
// 3. Elementos que chegam depois (paginação, modais, wire:navigate) são
//    inicializados pelo observer global de novos nós.
//
// O bundle é gerado por bin/build.js em resources/dist/filament-odometer-easy.js.
import 'number-flow'

const parseAttribute = (el, attribute) => {
    const raw = el.getAttribute(attribute)

    if (raw === null || raw === '') {
        return null
    }

    try {
        return JSON.parse(raw)
    } catch {
        return raw
    }
}

const configure = (el) => {
    const locales = parseAttribute(el, 'data-locales')
    const format = parseAttribute(el, 'data-format')

    if (locales !== null) {
        el.locales = locales
    }

    if (format !== null && typeof format === 'object') {
        el.format = format
    }

    applyTiming(el)
}

// data-duration (ms) controla a velocidade da animação. Sem o atributo,
// valem os timings padrão do number-flow (~900ms com easing de mola).
const applyTiming = (el) => {
    const duration = Number(el.getAttribute('data-duration'))

    if (!Number.isFinite(duration) || duration <= 0) {
        return
    }

    const easing = 'cubic-bezier(0.16, 1, 0.3, 1)'

    el.transformTiming = { duration, easing }
    el.spinTiming = { duration, easing }
    el.opacityTiming = { duration: Math.min(duration / 2, 450), easing: 'ease-out' }
}

const targetValue = (el) => {
    const value = Number(el.getAttribute('data-value'))

    return Number.isFinite(value) ? value : 0
}

const initialDelay = (el) => {
    const delay = Number(el.getAttribute('data-delay'))

    return Number.isFinite(delay) && delay >= 0 ? delay : 0
}

// data-start define de onde a animação inicial parte (padrão 0). Usado nos
// navigation badges re-renderizados pelo Livewire, para animar do valor
// anterior até o novo em vez de recomeçar do zero.
const startValue = (el) => {
    const start = Number(el.getAttribute('data-start'))

    return Number.isFinite(start) ? start : 0
}

const boot = (el) => {
    if (el.__odometerEasyBooted) {
        return
    }

    el.__odometerEasyBooted = true

    configure(el)

    // O primeiro update() do number-flow define o estado inicial sem animar:
    // partimos de data-start (0 por padrão) para que o próximo update()
    // anime até o valor real.
    el.update(startValue(el))

    setTimeout(() => el.update(targetValue(el)), initialDelay(el))

    // Livewire morph preserva o elemento e troca só os atributos: qualquer
    // mudança em data-value re-anima do valor atual para o novo.
    new MutationObserver(() => {
        configure(el)
        el.update(targetValue(el))
    }).observe(el, {
        attributes: true,
        attributeFilter: ['data-value', 'data-format', 'data-locales', 'data-duration'],
    })
}

// Navigation badges (OdometerNavigationBadge): a API do Filament
// (getNavigationBadge/NavigationItem::badge) só aceita string e escapa HTML,
// então o PHP envolve o valor com U+2060 (word joiner, invisível) e este
// script troca o texto do .fi-badge-label por um <number-flow>, com a config
// global exposta em window.filamentOdometerEasy pelo service provider.
const BADGE_PATTERN = /^\u2060(-?\d+(?:\.\d+)?)\u2060$/

// O markup interno varia entre versões do Filament (v3: texto direto no
// .fi-badge; v4/v5: .fi-badge-label): desce até o elemento mais profundo
// que ainda contém o valor marcado.
const findMarkedElement = (root) => {
    if (!BADGE_PATTERN.test(root.textContent.trim())) {
        return null
    }

    let el = root

    for (;;) {
        const child = [...el.children].find(
            (candidate) => BADGE_PATTERN.test(candidate.textContent.trim()),
        )

        if (!child) {
            return el
        }

        el = child
    }
}

const upgradeBadge = (badge) => {
    const label = findMarkedElement(badge)

    if (!label) {
        return
    }

    const match = BADGE_PATTERN.exec(label.textContent.trim())
    const config = window.filamentOdometerEasy ?? {}
    const el = document.createElement('number-flow')

    el.className = 'fi-odometer-easy'
    el.setAttribute('data-value', match[1])

    // Re-render do badge (Livewire morph) volta a ser texto marcado: anima
    // do valor anterior até o novo imediatamente, em vez de 0 -> novo.
    // O valor anterior fica no .fi-badge, que o morph preserva.
    const previous = badge.__odometerEasyValue

    el.setAttribute('data-delay', previous === undefined ? (config.delay ?? 500) : 0)

    if (previous !== undefined) {
        el.setAttribute('data-start', previous)
    }

    if (config.duration != null) {
        el.setAttribute('data-duration', config.duration)
    }

    if (config.locales != null) {
        el.setAttribute(
            'data-locales',
            typeof config.locales === 'string' ? config.locales : JSON.stringify(config.locales),
        )
    }

    if (config.format != null) {
        el.setAttribute('data-format', JSON.stringify(config.format))
    }

    badge.__odometerEasyValue = Number(match[1])
    label.replaceChildren(el)

    boot(el)
}

const upgradeBadgeOf = (node) => {
    const badge = node.parentElement?.closest('.fi-badge')

    if (badge) {
        upgradeBadge(badge)
    }
}

const scan = (root) => {
    if (root.matches?.('number-flow[data-value]')) {
        boot(root)
    }

    root.querySelectorAll?.('number-flow[data-value]').forEach(boot)

    if (root.matches?.('.fi-badge')) {
        upgradeBadge(root)
    }

    root.querySelectorAll?.('.fi-badge').forEach(upgradeBadge)
}

new MutationObserver((mutations) => {
    for (const mutation of mutations) {
        // Morph do Livewire pode só trocar o texto do badge, sem novos
        // elementos: characterData cobre esse caso.
        if (mutation.type === 'characterData') {
            upgradeBadgeOf(mutation.target)

            continue
        }

        mutation.addedNodes.forEach((node) => {
            if (node.nodeType === Node.ELEMENT_NODE) {
                scan(node)
            } else if (node.nodeType === Node.TEXT_NODE) {
                upgradeBadgeOf(node)
            }
        })
    }
}).observe(document.documentElement, {
    childList: true,
    subtree: true,
    characterData: true,
})

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => scan(document.documentElement))
} else {
    scan(document.documentElement)
}
