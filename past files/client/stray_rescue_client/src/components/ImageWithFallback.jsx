import { useState } from 'react'
import { ImageOff } from 'lucide-react'

function ImageWithFallback({ src, alt, className = '' }) {
  const [failed, setFailed] = useState(false)

  if (failed || !src) {
    return (
      <div
        className={`grid place-items-center bg-gradient-to-br from-orange-100 via-amber-50 to-white text-orange-400 ${className}`}
        role="img"
        aria-label={alt}
      >
        <div className="flex flex-col items-center gap-2">
          <ImageOff className="h-10 w-10" />
          <span className="text-sm font-semibold text-slate-500">Image unavailable</span>
        </div>
      </div>
    )
  }

  return <img alt={alt} className={className} onError={() => setFailed(true)} src={src} />
}

export { ImageWithFallback }
