/**
 * Court Image Gallery Component
 * Displays court images in a gallery format
 */

import { useState } from 'react';
import { ChevronLeft, ChevronRight, X } from 'lucide-react';

const CourtImageGallery = ({ images = [], mainImageUrl = null }) => {
  const [selectedIndex, setSelectedIndex] = useState(0);
  const [isModalOpen, setIsModalOpen] = useState(false);
  
  // Combine main image with gallery images
  const allImages = [];
  if (mainImageUrl && !images.some(img => img.image_url === mainImageUrl)) {
    allImages.push({ image_url: mainImageUrl, alt_text: 'Main court image', is_main: true });
  }
  allImages.push(...images);
  
  if (allImages.length === 0) {
    return (
      <div className="w-full h-64 bg-gray-200 rounded-xl flex items-center justify-center">
        <p className="text-gray-500">Nincs kép elérhető</p>
      </div>
    );
  }
  
  const nextImage = () => {
    setSelectedIndex((prev) => (prev + 1) % allImages.length);
  };
  
  const prevImage = () => {
    setSelectedIndex((prev) => (prev - 1 + allImages.length) % allImages.length);
  };
  
  const currentImage = allImages[selectedIndex];
  
  return (
    <>
      <div className="relative w-full">
        {/* Main image display */}
        <div className="relative w-full h-96 rounded-xl overflow-hidden bg-gray-100 shadow-lg">
          <img
            src={currentImage?.image_url || '/placeholder-court.jpg'}
            alt={currentImage?.alt_text || 'Court image'}
            className="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-300"
            onClick={() => setIsModalOpen(true)}
            onError={(e) => {
              e.target.src = '/placeholder-court.jpg';
            }}
          />
          
          {/* Navigation arrows */}
          {allImages.length > 1 && (
            <>
              <button
                onClick={prevImage}
                className="absolute left-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-all z-10"
                aria-label="Előző kép"
              >
                <ChevronLeft className="h-6 w-6" />
              </button>
              <button
                onClick={nextImage}
                className="absolute right-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-all z-10"
                aria-label="Következő kép"
              >
                <ChevronRight className="h-6 w-6" />
              </button>
            </>
          )}
          
          {/* Image counter */}
          {allImages.length > 1 && (
            <div className="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/50 text-white px-4 py-2 rounded-full text-sm">
              {selectedIndex + 1} / {allImages.length}
            </div>
          )}
        </div>
        
        {/* Thumbnail gallery */}
        {allImages.length > 1 && (
          <div className="grid grid-cols-4 gap-3 mt-4">
            {allImages.map((img, idx) => (
              <button
                key={idx}
                onClick={() => setSelectedIndex(idx)}
                className={`relative h-24 rounded-lg overflow-hidden border-2 transition-all ${
                  idx === selectedIndex
                    ? 'border-primary-500 ring-2 ring-primary-200'
                    : 'border-gray-200 hover:border-primary-300'
                }`}
              >
                <img
                  src={img.image_url || '/placeholder-court.jpg'}
                  alt={img.alt_text || 'Thumbnail'}
                  className="w-full h-full object-cover"
                  onError={(e) => {
                    e.target.src = '/placeholder-court.jpg';
                  }}
                />
              </button>
            ))}
          </div>
        )}
      </div>
      
      {/* Full screen modal */}
      {isModalOpen && (
        <div
          className="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4"
          onClick={() => setIsModalOpen(false)}
        >
          <button
            onClick={() => setIsModalOpen(false)}
            className="absolute top-4 right-4 text-white hover:text-gray-300 z-10"
          >
            <X className="h-8 w-8" />
          </button>
          
          <div className="relative max-w-7xl max-h-full" onClick={(e) => e.stopPropagation()}>
            <img
              src={currentImage?.image_url || '/placeholder-court.jpg'}
              alt={currentImage?.alt_text || 'Court image'}
              className="max-w-full max-h-[90vh] object-contain rounded-lg"
            />
            
            {allImages.length > 1 && (
              <>
                <button
                  onClick={prevImage}
                  className="absolute left-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-3 rounded-full"
                >
                  <ChevronLeft className="h-8 w-8" />
                </button>
                <button
                  onClick={nextImage}
                  className="absolute right-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-3 rounded-full"
                >
                  <ChevronRight className="h-8 w-8" />
                </button>
              </>
            )}
          </div>
        </div>
      )}
    </>
  );
};

export default CourtImageGallery;

